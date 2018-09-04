<?php
namespace controllers;

use models\Blog;

class BlogController
{
    // 日志列表
    public function index()
    {
        $blog = new Blog;
        // 搜索数据
        $data = $blog->search();
        // 加载视图
        view('blogs.index', $data);
    }
    //修改日志 (视图)
    public function edit(){
        $id = $_GET['id'];

        $model = new Blog;
        $blog = $model->find($id);
        
        view('blogs.edit',[
            'blog'=>$blog
        ]);
    }
    //修改日志 （提交）
    public function update(){
        $id = $_GET['id'];

        $model = new Blog;
        
        $model -> update([
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'is_show' => $_POST['is_show'],
        ],'id='.$id);

        // return redirect('/blog/index');

    }
    // 为所有的日志生成详情页
    public function content2html()
    {
        $blog = new Blog;
        $blog->content2html();
    }

    public function index2html()
    {
        $blog = new Blog;
        $blog->index2html();
    }

    public function display()
    {
        // 接收日志ID
        $id = (int)$_GET['id'];

        $blog = new Blog;

        // 把浏览量+1，并输出（如果内存中没有就查询数据库，如果内存中有直接操作内存）
        echo $blog->getDisplay($id);
        
    }

    public function displayToDb()
    {
        $blog = new Blog;
        $blog->displayToDb();
    }
}
