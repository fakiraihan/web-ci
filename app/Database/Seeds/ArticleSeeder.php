<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Getting Started with CodeIgniter 4',
                'content' => 'CodeIgniter 4 is a powerful and lightweight PHP framework that makes it easy to build web applications. In this article, we\'ll explore the key features and benefits of using CodeIgniter 4 for your next project.

CodeIgniter 4 comes with many improvements over its predecessor, including better performance, enhanced security features, and a more modern codebase. The framework follows the MVC (Model-View-Controller) pattern, which helps organize your code and makes it more maintainable.

Some of the key features of CodeIgniter 4 include:
- Improved performance and smaller footprint
- PSR-4 autoloading support
- Better error handling and debugging
- Enhanced security features
- Modern PHP features support

Whether you\'re a beginner or an experienced developer, CodeIgniter 4 provides the tools you need to build robust web applications quickly and efficiently.',
                'category' => 'web-development',
                'image_url' => 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'John Developer',
                'author_image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
            ],
            [
                'title' => 'Modern PHP Development Best Practices',
                'content' => 'PHP has evolved significantly over the years, and modern PHP development follows several best practices that help create maintainable, secure, and efficient applications.

Here are some essential best practices for modern PHP development:

1. Use Composer for dependency management
2. Follow PSR standards for coding style and autoloading
3. Implement proper error handling and logging
4. Use environment variables for configuration
5. Write unit tests for your code
6. Sanitize and validate all user input
7. Use prepared statements for database queries
8. Keep your PHP version up to date

By following these practices, you can ensure that your PHP applications are robust, secure, and maintainable. Modern PHP frameworks like CodeIgniter 4, Laravel, and Symfony make it easier to implement these best practices.',
                'category' => 'programming',
                'image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'Sarah Wilson',
                'author_image' => 'https://images.unsplash.com/photo-1494790108755-2616b612b5c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'title' => 'Building RESTful APIs with CodeIgniter 4',
                'content' => 'RESTful APIs are essential for modern web applications, especially when building single-page applications or mobile apps. CodeIgniter 4 provides excellent support for building RESTful APIs.

In this tutorial, we\'ll cover:

1. Setting up routes for API endpoints
2. Creating API controllers
3. Handling different HTTP methods (GET, POST, PUT, DELETE)
4. JSON response formatting
5. Authentication and authorization
6. Error handling and status codes
7. API documentation best practices

CodeIgniter 4\'s built-in features make it straightforward to create well-structured APIs that follow REST principles. The framework\'s lightweight nature also means your APIs will be fast and efficient.

We\'ll walk through creating a complete API for a blog application, including endpoints for managing articles, users, and authentication.',
                'category' => 'tutorials',
                'image_url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'author_name' => 'Mike Chen',
                'author_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
        ];

        // Using Query Builder to insert data
        $this->db->table('articles')->insertBatch($data);
    }
}
