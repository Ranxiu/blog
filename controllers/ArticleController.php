<?php
namespace controllers;

use models\Article;

class ArticleController
{
    public function main()
    {
        // 取数据
        $Article = new Article;
        $blog = $Article->getAll();
        // var_dump($data);
        // die();
        // 加载视图
        return view('wirte.blog_list', [
            'blog' => $blog
        ]);
    }
}