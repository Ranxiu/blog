<?php
namespace controllers;

class IndexController
{
    public function index()
    {
        // 取最新的日志
        $blog = new \models\Blog;
        $blogs = $blog->getNewBlog();
      
        // 取取活跃用户
      
        $users = $blog->getActiveUsers();

        // 显示页面
        view('index.index', [
            'blogs' => $blogs,
            'users' => $users,
        ]);
    }
}