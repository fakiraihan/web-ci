<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        // If already logged in, redirect to articles
        if (session()->get('user_id')) {
            return redirect()->to('/articles');
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByUsername($username);

        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            // Set session data
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'is_admin' => $user['is_admin'],
                'logged_in' => true,
            ]);

            return redirect()->to('/articles')->with('success', 'Welcome back, ' . $user['username'] . '!');
        }

        return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
    }

    public function register()
    {
        // If already logged in, redirect to articles
        if (session()->get('user_id')) {
            return redirect()->to('/articles');
        }

        return view('auth/register');
    }

    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'is_admin' => false,
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/login')->with('success', 'Registration successful! Please login.');
        }

        return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }
}
