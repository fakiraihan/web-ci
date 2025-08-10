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

    public function category($category)
    {
        $data['articles'] = $this->articleModel->getArticlesByCategory($category);
        $data['category'] = ucfirst($category);
        $data['recentArticles'] = $this->articleModel->getRecentArticles(5);

        return view('articles/category', $data);
    }
}
