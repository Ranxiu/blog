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
    //添加日志
    public function create(){
        // 加载视图
        view('blogs.create');

    }
    //添加日志 (视图)
    public function store(){

        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];

        $blog = new Blog;
        $blog->add($title,$content,$is_show);

        // 跳转
        message('发表成功', 2, '/blog/index');
    }
    // 显示私有日志
    public function content()
    {
        // 1. 接收ID，并取出日志信息
        $id = $_GET['id'];
        $model = new Blog;
        $blog = $model->find($id);

        // 2. 判断这个日志是不是我的日志
        if($_SESSION['id'] != $blog['user_id'])
            die('无权访问！');

        // 3. 加载视图
        view('blogs.content', [
            'blog' => $blog,
        ]);

    }
   
    // 为所有的日志生成详情页
    public function content2html()
    {
        $blog = new Blog;
        $blog->content2html();
    }

    //删除日志
    public function delete(){
        $id = $_GET['id'];

        $blog = new Blog;
        
        $blog->delete($id);

        message('删除成功',2,'/blog/index');
    }

    //修改日志显示视图
    public function edit(){
        $id = $_GET['id'];
        //根据ID取出日志的信息
        $blog = new Blog;
        $data = $blog->find($id);
        view('blogs.edit',[
            'data'=>$data,
        ]);
    }
    //修改日志表单提交
    public function update(){
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];

        $blog = new Blog;
        $mes = $blog->update($title,$content,$is_show,$id);
        // var_dump($mes);
        if($mes){
            message('修改成功！',2,'/blog/index');
        } else{
            message('修改失败！',2,'/blog/index');
        }
       
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
        $display =  $blog->getDisplay($id);

        //返回多个数据是必须要用JSON

        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email']:''
        ]);
        
    }

    public function displayToDb()
    {
        $blog = new Blog;
        $blog->displayToDb();
    }
}
