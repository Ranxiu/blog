<?php
define('ROOT',dirname(__FILE__).'/../'); //项目根目录

//引入composer 自动加载文件
require(ROOT.'vendor/autoload.php');

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
    //如果穿了数据，就把数组展开成变量
    if($data)
    {
        extract($data);

        //加载视图文件
        require ROOT.'views/'.str_replace('.','/',$file).'.html';
    }
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
