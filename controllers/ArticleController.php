<?php
namespace controllers;

use models\Article;

class ArticleController
{
    public function list()
    {
        // 取数据
        $Article = new Article;
        $data = $Article->getName();

        // 加载视图
        return view('articles.list', [
            'data' => $data
        ]);
    }
}