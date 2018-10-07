<?php
namespace controllers;

use models\Blog;

class BlogController
{   

    //取出排行榜的分值 （日志表,评论表,点赞表）
    public function activeUsers(){

        $model = new BLog;

        $model->activeUsers();

       

    }
    //获取点赞的用户
    public function agreements_list()
    {
        $id = $_GET['id'];

        // 获取这个日志所有点赞的用户
        $model = new \models\Blog;
        $data = $model->agreeList($id);

        // 转成 JSON 返回 
        echo json_encode([
            'status_code' => 200,
            'data' => $data,
        ]);

    }

    // 点赞
    public function agreements()
    {
        $id = $_GET['id'];
        // 判断登录
        if(!isset($_SESSION['id']))
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '必须先登录'
            ]);
            exit;
        }

        // 点赞
        $model = new \models\Blog;
        $ret = $model->zan($id);
        if($ret)
        {
            echo json_encode([
                'status_code' => '200',
            ]);
            exit;
        }
        else
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '已经点赞过了'
            ]);
            exit;
        }
    }



    // 日志列表
    public function getBlog()
    {   

        if(isset($_GET['pid']))
        {
            $id = $_GET['id'];
            $blog = new Blog;
            $data = $blog->getBlog($id);
            if($_GET['id']==1){
                $t['name'] = '学无止境';
            }else if($_GET['id']==2){
                $t['name'] = '慢生活';
            }
            view('blogs/index',[
                'data'=> $data,
                't' => $t,
            ]);

        }else {
            $id = $_GET['id'];
            $blog = new Blog;
        
            $data = $blog->getBlogs($id);
            
            $t = $blog->getTypename($id);       

            // var_dump($data);
            view('blogs/index',[
                'data'=> $data,
                't' => $t,
            ]);
        }
       
    }
    //获取日志详情页
    public function info(){
        $blog = new \models\Blog;
        $id = $_GET['id'];
        $data = $blog->getBlogContent($id);

        view('blogs.info',[
            'data'=> $data,
        ]);
    }
    //时间轴
    public function time(){
        view('blogs.time');
    }
    //关于我
    public function about(){
        view('blogs.about');
    }
    //留言页面
    public function gbook(){
        view('blogs.gbook');
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
