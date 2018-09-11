<?php
namespace models;

use PDO;

class User extends Base
{
    
    public function add($email,$password)
    {
        $stmt = self::$pdo->prepare("INSERT INTO users (email,password) VALUES(?,?)");
        return $stmt->execute([
                                $email,
                                $password,
                            ]);
    }

    public function login($email,$password)
    {
        $stmt = self::$pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
        $stmt->execute([
            $email,
            $password,
        ]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($user){
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['money'] = $user['money'];
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    // 为用户增加金额
    public function addMoney($money, $userId)
    {
        $stmt = self::$pdo->prepare("UPDATE users SET money=money+? WHERE id=?");
        return $stmt->execute([
            $money,
            $userId
        ]);

    }

    // 获取余额
    public function getMoney()
    {
        $id = $_SESSION['id'];
        $redis = \libs\Redis::getInstance();
        $key = 'user_money:'.$id;

        $money = $redis->get($key);
        if($money)
            return $money;
        else
        {
            $stmt = self::$pdo->prepare('SELECT money FROM users WHERE id = ?');
            $stmt->execute([$id]);
            $money = $stmt->fetch( PDO::FETCH_COLUMN );
            // 保存到 Redis
            $redis->set($key, $money);
            return $money;
        }
    }
      

}