<?php
namespace controllers;

// 引入模型类
use models\User;

class UserController
{
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

    public function setinfo()
    {
        $user = new User;
        $data = $user->articles();
        var_dump($data);
        // 加载视图
        view('users.hello', [
            'data' => $data
        ]);
    }

    public function world()
    {
        echo 'world';
    }
}