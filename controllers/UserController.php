<?php
namespace controllers;

// 引入模型类
use models\User;
use models\Order;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Intervention\Image\ImageManagerStatic as image;
// use libs\Redis;

class UserController
{   

    // 设置头像
    public function setavatar()
    {
        // 上传新头像
        $upload = \libs\Uploader::make();
        $path = $upload->upload('avatar', 'avatar');
        
        // 裁切图片
        $image = Image::make(ROOT . 'public/uploads/'.$path);
        // 注意：Crop 参数必须是整数，所以需要转成整数：(int)
        $image->crop((int)$_POST['w'], (int)$_POST['h'], (int)$_POST['x'], (int)$_POST['y']);
        // 保存时覆盖原图
        $image->save(ROOT . 'public/uploads/'.$path);

        // 保存到 user 表中
        $model = new User;
        $data =  $model->setAvatar('/uploads/'.$path);
        


        // 注意：网站中图片有两个路径
        // 浏览器（从网站根目录开始找）： /uploads/avatar/20180914/041a05ec7f7179dab8e00b13de997f1a.jpg
        // 硬盘上的路径 :    D:/www/blog/7f7179dab8e00b13de997f1a.jpg
        // 删除原头像
        @unlink( ROOT . 'public'.$_SESSION['avatar'] );

        // 设置新头像
        $_SESSION['avatar'] = '/uploads/'.$path;


        message('设置成功', 2, '/blog/index');
    }



    //显示批量上传视图
    public function douploadm(){
        view('users.uploads');
    }
    //批量上传处理函数
    public function uploadall(){
        $upload = \libs\Uploader::make();
        //参数一、 表单中的文件名
        //参数二、 保存到二级目录名
        $path = $upload->uploadall('many');
    }

    //显示大文件上传的视图
    public function uploadBig(){
        view('users.upbig');
    }

    //处理大文件函数
    public function uploadbigimg(){
         /* 接收提交的数据 */
        $count = $_POST['count'];  // 总的数量
        $i =$_POST['i'];        // 当前是第几块
        // var_dump( $serve,$i);
        $size = $_POST['size'];   // 每块大小
        $name = 'big_img_'.$_POST['img_name'];  // 所有分块的名字
        $img = $_FILES['img'];    // 图片
        /* 保存每个分片 */
        
        move_uploaded_file( $img['tmp_name'] , ROOT.'tmp/'.$i);
        /* 最后一个图片上传成功之后，合并所有图片 */
        // 思考：如何判断是否所有图片都已经上传成功？
        // 难点：因为每个分块到达服务器的顺序不固定，所以我们不能根据顺序来判断是否都上传成功。
        // 实现思路：每上传一个就+1，直到上传的数量等于总的数量
        $redis = \libs\Redis::getInstance();
        // 每上传一张就加1
        $uploadedCount = $redis->incr($name);
        // 如果是最后一个分支就合并
        if($uploadedCount == $count)
        {
            // 以追回的方式创建并打开最终的大文件
            $fp = fopen(ROOT.'public/uploads/big/'.$name.'.png', 'a');
            // 循环所有的分片
            for($i=0; $i<$count; $i++)
            {
                // 读取第 i 号文件并写到大文件中
                fwrite($fp, file_get_contents(ROOT.'tmp/'.$i));
                // 删除第 i 号临时文件
                unlink(ROOT.'tmp/'.$i);
            }
            // 关闭文件
            fclose($fp);
            // 从 redis 中删除这个文件对应的编号这个变量
            $redis->del($name);
        }
    }
    
     // 显示注册视图
    public function register()
    {
        view('users.add');
    }


    //显示上传头像视图
    public function avatar(){
        view('users.avatar');
    }
    //服务器添加取余额接口
    public function money(){
        $user = new User;
        echo $user->getMoney();
    }

    //充值方法
    public function docharge(){
        //生成定单
        $money = $_POST['money'];
        $model = new Order;
        
        $model->create($money);
        message('充值订单已生成，请立即支付！',2,'/user/orders');
    }
    //显示所有订单页面
    public function orders(){

        $order = new Order;

        //搜索数据
        $data = $order->search();

        //加载视图

        view('users.order',$data);
    }
    
    public function hello()
    {
        // 取数据
        $user = new User;
        $name = $user->getName();

        // 加载视图
        view('users.hello', [
            'name' => $name
        ]);
    }

    public function world()
    {
        echo 'world';
    }

    //显示充值视图
    public function charge(){
        
        view('users.charge');
    }

    //查询订单状态的接口
    public function orderStatus(){
        $sn  = $_GET['sn'];

        $try = 5;
        $model = new Order;

        do{
            //查询订单信息
            $info = $model->findBySn($sn);
            //如果订单未支付就等待1秒，并减少尝试的次数，如果已经支付就退出循环
            if($info['status']==0){
                sleep(1);
                $try--;
            }else{
                break;
            }
        }while($try>0); 
        
        echo $info['status'];
    }

    //呈现登陆界面
    public function login(){

        view('users.login');
    }
    
    //退出登陆
    public function logout(){

        unset($_SESSION['id']);
      
        message('退出成功',2,'/user/login');
    }

    //登陆校验
    public function dologin(){
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        // var_dump($password);
        $user = new \models\User;

        if($user->login($email,$password))
        {
            message('登陆成功！',2,'/blog/index');
        }
        else{
            message('用户名或者密码错误' ,1, '/user/login');
        }
    }

    public function store()
    {
        // 1. 接收表单
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        // 2. 生成激活码(32位的随机的字符串)（原则：让用户猜不出来规律）
        $code = md5( rand(1,99999) );

        // 3. 保存到 Redis
        $redis = \libs\Redis::getInstance();
        // 序列化（数组转成 JSON 字符串）
        $value = json_encode([
            'email' => $email,
            'password' => $password,
        ]);
        // 键名
        $key = "temp_user:{$code}";
        $redis->setex($key, 300, $value);

        // 4. 把激活码发送到用户的账邮箱中
        // 从邮箱地址中取出姓名 
        $name = explode('@', $email);
        // 构造收件人地址[    fortheday@126.com   ,    fortheday  ]
        $from = [$email, $name[0]];
        // 构造消息数组
        $message = [
            'title' => '智聊系统-账号激活',
            'content' => "点击以下链接进行激活：<br> 点击激活：
            <a href='http://localhost:8000/user/active_user?code={$code}'>
            http://localhost:8000/user/active_user?code={$code}</a><p>
            如果按钮不能点击，请复制上面链接地址，在浏览器中访问来激活账号！</p>",
            'from' => $from,
        ];
        // 把消息转成字符串(JSON ==> 序列化)
        $message = json_encode($message);
   
        // 放到队列中
        // $redis = \libs\Redis::getInstance();
      
        $redis->lpush('email', $message);

      


        echo 'ok';

    }

    public function active_user()
    {
        // 1. 接收激活码
        $code = $_GET['code'];

        // 2. 到 Redis 取出账号
        $redis = \libs\Redis::getInstance();
        // 拼出名字
        $key = 'temp_user:'.$code;
        // 取出数据
        $data = $redis->get($key);
        // 判断有没有
        if($data)
        {
            // 从 redis 中删除激活码
            $redis->del($key);
            // 反序列化（转回数组）
            $data = json_decode($data, true);
            // 插入到数据库中
            $user = new \models\User;
            // var_dump($data['email'], $data['password']);
            $user->add($data['email'], $data['password']);
            // 跳转到登录页面
            header('Location:/user/login');
        }
        else
        {
            die('激活码无效！');
        }
    }
    // 获取最新的10个日志
    public function makeExcel()
    {
        // 获取当前标签页
        $spreadsheet = new Spreadsheet();
        // 获取当前工作
        $sheet = $spreadsheet->getActiveSheet();

        // 设置第1行内容
        $sheet->setCellValue('A1', '标题');
        $sheet->setCellValue('B1', '内容');
        $sheet->setCellValue('C1', '发表时间');
        $sheet->setCellValue('D1', '是发公开');

        // 取出数据库中的日志
        $model = new \models\Blog;
        // 获取最新的10个日志
        $blogs = $model->getNew();

        $i=2; // 第几行
        foreach($blogs as $v)
        {
            $sheet->setCellValue('A'.$i, $v['title']);
            $sheet->setCellValue('B'.$i, $v['content']);
            $sheet->setCellValue('C'.$i, $v['created_at']);
            $sheet->setCellValue('D'.$i, $v['is_show']);
            $i++;
        }

        $date = date('Ymd');

        // 生成 excel 文件
        $writer = new Xlsx($spreadsheet);
        $writer->save(ROOT . 'excel/'.$date.'.xlsx');

        // 调用 header 函数设置协议头，告诉浏览器开始下载文件

        // 下载文件路径
        $file = ROOT . 'excel/'.$date.'.xlsx';
        // 下载时文件名
        $fileName = '最新的20条日志-'.$date.'.xlsx';

        // 告诉浏览器这是一个二进程文件流    
        Header ( "Content-Type: application/octet-stream" ); 
        // 请求范围的度量单位  
        Header ( "Accept-Ranges: bytes" );  
        // 告诉浏览器文件尺寸    
        Header ( "Accept-Length: " . filesize ( $file ) );  
        // 开始下载，下载时的文件名
        Header ( "Content-Disposition: attachment; filename=" . $fileName );    

        // 读取服务器上的一个文件并以文件流的形式输出给浏览器
        readfile($file);
    }

}