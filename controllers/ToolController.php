<?php
namespace controllers;

use models\User;

class ToolController {

    //获取数据库所有账号
    public function  users(){
        $user = new User;
        $data = $user->getAll();
        echo json_encode([
            'status_code'=>200,
            'data'=>$data,
        ]);
    }

    //切换账号

    public function login(){
        $email = $_GET['email'];
        //退出
        $_SESSION = [];
        //重新登陆
        $user = new \models\User;
        $user->login($email,md5(123123));
    }


}


?>