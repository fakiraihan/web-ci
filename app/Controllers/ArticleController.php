<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use CodeIgniter\Controller;

class ArticleController extends Controller
{
    protected $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    public function index()
    {
        $data['articles'] = $this->articleModel->getPublishedArticles();
        $data['recentArticles'] = $this->articleModel->getRecentArticles(5);
        
        return view('articles/index', $data);
    }

    public function show($id)
    {
        $article = $this->articleModel->find($id);

        if (!$article || $article['status'] !== 'published') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Article not found');
        }

        $commentModel = new \App\Models\CommentModel();
        // VULN: SQL Injection - for research only
        $comments = $commentModel->getCommentsByArticleIdVuln($id); // $id is not sanitized

        $data['article'] = $article;
        $data['comments'] = $comments;
        $data['recentArticles'] = $this->articleModel->getRecentArticles(5);

        return view('articles/show', $data);
    }

    // VULN: Brutal SQL Injection endpoint for testing
    public function searchVuln($search)
    {
        $db = \Config\Database::connect();
        // DIRECT SQL INJECTION - NO SANITIZATION!
        $sql = "SELECT * FROM articles WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
        $query = $db->query($sql);
        $results = $query->getResultArray();
        
        $data['results'] = $results;
        $data['search'] = $search;
        return view('articles/search_results', $data);
    }

    // VULN: Another brutal SQL injection endpoint
    public function getUserVuln($userId)
    {
        $db = \Config\Database::connect();
        // DIRECT SQL INJECTION - NO SANITIZATION!
        $sql = "SELECT * FROM users WHERE id = $userId";
        $query = $db->query($sql);
        $user = $query->getRowArray();
        
        if ($user) {
            echo "<h1>User Info (VULNERABLE!)</h1>";
            echo "<p>ID: " . $user['id'] . "</p>";
            echo "<p>Username: " . $user['username'] . "</p>";
            echo "<p>Is Admin: " . ($user['is_admin'] ? 'Yes' : 'No') . "</p>";
        } else {
            echo "<h1>User not found</h1>";
        }
    }

    public function category($category)
    {
        $data['articles'] = $this->articleModel->getArticlesByCategory($category);
        $data['category'] = ucfirst($category);
        $data['recentArticles'] = $this->articleModel->getRecentArticles(5);

        return view('articles/category', $data);
    }
}
