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
    //根据id获取日志详情页
    public function info(){
        $blog = new \models\Blog;
        $id = $_GET['id'];
        $data = $blog->getBlogContent($id);

        view('blogs.info',[
            'data'=> $data,
        ]);
    }
    //获取特别推荐日志 
    public function getTbtj(){
        $blog = new \models\Blog;
        $data = $blog->getTbtj();
        // echo '<pre>';
        // var_dump($data);
        view('common.tbtj',[
            'data'=>$data,
        ]);
    }
    //普通推荐文章 1大4小
    public function getPttj(){
        $blog = new \models\Blog;
        $data = $blog->getPttj();
        // echo '<pre>';
        // var_dump($data);
        view('common.pttj',[
            'data'=>$data,
        ]);
    }
    //普通推荐文章 1大4小
    public function getDjpx(){
        $blog = new \models\Blog;
        $data = $blog->getDjpx();
        // echo '<pre>';
        // var_dump($data);
        view('common.djpx',[
            'data'=>$data,
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
        //取出一级分类
        $blog = new \models\Blog;
        $topCat = $blog->getType();
        
        view('blogs.create',[
            'topCat'=>$topCat,
        ]);
    }


    //根据一级分类ID取出二级分类
    public function ajax_get_cat(){

        $id = (int)$_GET['id'];
        $blog = new \models\Blog;
        // 根据这个ID查询子分类
        $data = $blog->ajax_get_cat($id);
        // 转成 JSON
        echo json_encode($data);

    }

    // 处理添加日志表单
    public function insert()
    {   
        //新注册用户注册三天内不能发布日志
        $user = new \models\User;
        $time = $user->getTime();//259200=3天
        $oldtime = strtotime($time); //账号注册时的时间戳
        $newtime = time(); //当前时间戳
        //②每人每天的发帖不能超过5篇或者超过5篇后多余的不予显示
        //$today = date('Y-m-d 0:0:0');
        //$sql = 'select count(*) from blogs where created_at >= $today '
        //③删除某人某个时间段之后的帖子。
        //$time = 'Y-m-d H:i:s';
        //$sql = 'delete from blogs where user_id=1 and created_at >=$time'

        // if($oldtime-259200>$newtime){
           
            // var_dump($_POST);die;
            $model = new \models\Blog;
            $model->fill($_POST);
            $model->insert();
            redirect('/index/index');
        // }else{
        //     echo '很抱歉！您的账号注册时间小于三天，所以您暂时无法发布日志';
        // }
        
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

        message('删除成功',2,'/index/index');
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
