<?php

namespace App\Controllers;

use App\Models\ArticleModel;
use CodeIgniter\Controller;

class AdminController extends Controller
{
    protected $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    public function index()
    {
        $data['articles'] = $this->articleModel->orderBy('created_at', 'DESC')->findAll();
        
        return view('admin/index', $data);
    }

    public function create()
    {
        return view('admin/create');
    }

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]',
            'category' => 'required|max_length[100]',
            'author_name' => 'required|max_length[100]',
            'status' => 'required|in_list[published,draft]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'category' => $this->request->getPost('category'),
            'image_url' => $this->request->getPost('image_url'),
            'author_name' => $this->request->getPost('author_name'),
            'author_image' => $this->request->getPost('author_image'),
            'status' => $this->request->getPost('status'),
        ];

        if ($this->articleModel->insert($data)) {
            return redirect()->to('/admin')->with('success', 'Article created successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create article. Please try again.');
    }

    public function edit($id)
    {
        $article = $this->articleModel->find($id);

        if (!$article) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Article not found');
        }

        $data['article'] = $article;

        return view('admin/edit', $data);
    }

    public function update($id)
    {
        $article = $this->articleModel->find($id);

        if (!$article) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Article not found');
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]',
            'category' => 'required|max_length[100]',
            'author_name' => 'required|max_length[100]',
            'status' => 'required|in_list[published,draft]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'category' => $this->request->getPost('category'),
            'image_url' => $this->request->getPost('image_url'),
            'author_name' => $this->request->getPost('author_name'),
            'author_image' => $this->request->getPost('author_image'),
            'status' => $this->request->getPost('status'),
        ];

        if ($this->articleModel->update($id, $data)) {
            return redirect()->to('/admin')->with('success', 'Article updated successfully!');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update article. Please try again.');
    }

    public function delete($id)
    {
        $article = $this->articleModel->find($id);

        if (!$article) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Article not found');
        }

        if ($this->articleModel->delete($id)) {
            return redirect()->to('/admin')->with('success', 'Article deleted successfully!');
        }

        return redirect()->to('/admin')->with('error', 'Failed to delete article. Please try again.');
    }
}
