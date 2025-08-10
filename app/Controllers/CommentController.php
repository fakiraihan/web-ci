<?php
namespace App\Controllers;

use App\Models\CommentModel;
use App\Models\ArticleModel;
use CodeIgniter\Controller;

class CommentController extends Controller
{
    protected $commentModel;
    protected $articleModel;
    public function __construct()
    {
        $this->commentModel = new CommentModel();
        $this->articleModel = new ArticleModel();
    }

    public function store($articleId)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }
        $content = $this->request->getPost('content');
        if (!$content || strlen($content) > 1000) {
            return redirect()->back()->with('error', 'Comment is required and must be less than 1000 characters.');
        }
        $article = $this->articleModel->find($articleId);
        if (!$article) {
            return redirect()->back()->with('error', 'Article not found.');
        }
        $this->commentModel->insert([
            'article_id' => $articleId,
            'user_id' => session()->get('user_id'),
            'content' => $content,
        ]);
        return redirect()->back()->with('success', 'Comment posted!');
    }

    // VULN: IDOR - for research only
    public function delete($id)
    {
        $commentModel = new CommentModel();
        // No ownership or admin check
        $commentModel->delete($id);
        return redirect()->back()->with('success', 'Comment deleted (no auth check).');
    }
}
