<?php
define('ROOT',dirname(__FILE__).'/../'); //项目根目录

//引入composer 自动加载文件
require(ROOT.'vendor/autoload.php');

// 动态的修改 php.ini 配置文件
ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=1');  // 设置 redis 服务器的地址、端口、使用的数据库
ini_set('session.gc_maxlifetime', 600);   // 设置 SESSION 10分钟过期

//开启SESSION
session_start();

//类加载函数
function autoLoadClass($class)
{
    require_once ROOT.str_replace('\\','/',$class).'.php';
}

//注册加载函数
spl_autoload_register('autoLoadClass');


//视图加载函数 
function view($file,$data=[])
{
    
   // 解压数组成变量
   extract($data);

   $path = str_replace('.', '/', $file) . '.html';

   // 加载视图
   require(ROOT . 'views/' . $path);
}
// 获取 GET 参数
function getUrlParams($except = [])
{
    // ['odby','odway']
    // 循环删除变量
    foreach($except as $v)
    {
        unset($_GET[$v]);

        // unset($_GET['odby']);
        // unset($_GET['odway']);
    }

    /*
    $_GET['keyword'] = 'xzb';
    $_GET['is_show] = 1

    // 拼出：  keyword=abc&is_show=1
    */

    $str = '';
    foreach($_GET as $k => $v)
    {
        $str .= "$k=$v&";
    }

    return $str;
}
//重定向
function redirect($url)
{
    header('Location:' . $url);
    exit;
}
//过滤xss攻击函数  <?=e($title)
function e($content){

    return htmlspecialchars($content);
}
// 跳回上一个页面
function back()
{
    redirect( $_SERVER['HTTP_REFERER'] );
}
//获取配置文件
function config($name){
    static $config = null;
    if($config === null){
        //引入配置文件
        $config = require(ROOT.'config.php');
    }
    return $config[$name];
}

// 添加路由 ：解析 URL 上的路径： 控制器/方法
// 获取 URL 上的路径

// 添加路由 ：解析 URL 浏览器上 blog/index  CLI中就是 blog index

if(php_sapi_name() == 'cli')
{
    $controller = ucfirst($argv[1]) . 'Controller';
    $action = $argv[2];
}
else
{
    if( isset($_SERVER['PATH_INFO']) )
    {
        $pathInfo = $_SERVER['PATH_INFO'];
        // 根据 / 转成数组
        $pathInfo = explode('/', $pathInfo);

        // 得到控制器名和方法名 ：
        $controller = ucfirst($pathInfo[1]) . 'Controller';
        $action = $pathInfo[2];
    }
    else
    {
        // 默认控制器和方法
        $controller = 'IndexController';
        $action = 'index';
    }
}
// 为控制器添加命名空间
$fullController = 'controllers\\'.$controller;


$_C = new $fullController;
$_C->$action();

// 提示消息的函数
// type 0:alert   1:显示单独的消息页面  2：在下一个页面显示
// 说明：$seconds 只有在 type=1时有效，代码几秒自动跳动
function message($message, $type, $url, $seconds = 5)
{
    if($type == 0)
    {
        echo "<script>alert('{$message}');location.href='{$url}';</script>";
        exit;

    }
    else if($type == 1)
    {
        // 加载消息页面
        view('common.success', [
            'message' => $message,
            'url' => $url,
            'seconds' => $seconds
        ]);
    }
    else if($type==2)
    {
        // 把消息保存到 SESSION
        $_SESSION['_MESS_'] = $message;
        // 跳转到下一个页面
        redirect($url);
    }
}

?>