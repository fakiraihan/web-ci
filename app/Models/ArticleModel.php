<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table = 'articles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'title', 
        'content', 
        'category', 
        'image_url', 
        'author_name', 
        'author_image', 
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]',
        'category' => 'required|max_length[100]',
        'author_name' => 'required|max_length[100]',
        'status' => 'required|in_list[published,draft]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getPublishedArticles()
    {
        return $this->where('status', 'published')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    public function getArticlesByCategory($category)
    {
        return $this->where('category', $category)
                   ->where('status', 'published')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    public function getRecentArticles($limit = 5)
    {
        return $this->where('status', 'published')
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}
