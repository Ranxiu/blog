<?php
/*所有其他模型的父模型*/
namespace models;
use PDO;

class Base {

    //保存PDO对象
    public static $pdo;

    public function __construct()
    {   
        if(self::$pdo === null){
            // 取日志的数据
            self::$pdo = new PDO('mysql:host=127.0.0.1;dbname=blog', 'root', '573511');
            self::$pdo->exec('SET NAMES utf8');
        }
        
    }
}
