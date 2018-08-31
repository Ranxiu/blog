<?php
namespace controllers;

use models\Article;

class IndexController
{
    public function index()
    {
       // 取数据
       $Article = new Article;
       $data = $Article->getAll();
       // var_dump($data);
       // die();
       // 加载视图
       return view('articles.index', [
           'data' => $data
       ]);
    }

    public function list()
    {
       // 取数据
       $Article = new Article;
       $data = $Article->getAll();
       // var_dump($data);
       // die();
       // 加载视图
       return view('articles.index', [
           'data' => $data
       ]);
    }

}