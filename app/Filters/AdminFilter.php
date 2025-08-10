<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in and is admin
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Please login to access this page.');
        }

        if (!session()->get('is_admin')) {
            return redirect()->to('/articles')->with('error', 'Access denied. Admin privileges required.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
    }
}
