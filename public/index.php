<?php
define('ROOT',dirname(__FILE__).'/../'); //项目根目录
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
        require_once ROOT.'views/'.str_replace('.','/',$file).'.html';
    }
}

// 添加路由 ：解析 URL 上的路径： 控制器/方法
// 获取 URL 上的路径

if(isset($_SERVER['PATH_INFO']))
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

// 为控制器添加命名空间
$fullController = 'controllers\\'.$controller;


$_C = new $fullController;
$_C->$action();
