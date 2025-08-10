<?php
namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['article_id', 'user_id', 'content'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function withUser()
    {
        return $this->select('comments.*, users.username')
            ->join('users', 'users.id = comments.user_id');
    }

    // VULN: SQL Injection - for research only
    public function getCommentsByArticleIdVuln($articleId)
    {
        $sql = "SELECT comments.*, users.username FROM comments JOIN users ON users.id = comments.user_id WHERE article_id = $articleId ORDER BY created_at ASC";
        return $this->db->query($sql)->getResultArray();
    }
}
