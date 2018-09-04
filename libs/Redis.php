<?php
namespace libs;

class Redis {

    private static $redis = null;
    private function __clone(){}
    private function __construct(){}

    //获取redis对象
    public static function getInstance(){
        //如果还没有 redis就生成一个
        //只会连接一次
        if(self::$redis === null){
            //放到队列中
           self::$redis = new \Predis\Client([
                'scheme' =>'tcp',
                'host' => '127.0.0.1',
                'port' => 32768,
           ]);
           return self::$redis;
        }
    }
}