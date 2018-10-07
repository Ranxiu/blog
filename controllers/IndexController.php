<?php
namespace controllers;
use \model\Blog;

class IndexController
{
    public function index()
    {
        // 取最新的日志
        $blog = new \models\Blog;
        $blogs = $blog->getNewBlog();
        
        // 取出活跃用户
        $users = $blog->getActiveUsers();

        // 显示页面
        view('index.index', [
            'blogs' => $blogs,
            'users' => $users,
        ]);
    }
    public function nav()
    {
        $blog = new \models\Blog;
        //取出一级分类
        $type = $blog->getType();
        //取出二级分类
        $types = $blog->getTypes();

       
        view('common.nav', [
            'type' => $type,
            'types' => $types,
        ]);
    }
}