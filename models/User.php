<?php
namespace models;

use PDO;

class User extends Base
{   
    //获取所有用户
    public function getAll(){
        $stmt = self::$pdo->query('SELECT * FROM users');

        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //设置头像
    public function setAvatar($path)
    {
        $stmt = self::$pdo->prepare('UPDATE users SET avatar=? WHERE id=?');
        $stmt->execute([
            $path,
            $_SESSION['id']
        ]);
    }


    
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
            $_SESSION['avatar'] = $user['avatar'];
           
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
        
        //根据当前用户id查询当前数据库中的余额
        $stmt = self::$pdo->prepare('SELECT money FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $money = $stmt->fetch( PDO::FETCH_COLUMN );
        // 更新到SESSION中
        $_SESSION['money'] = $money;
        
        return $money;
        
    }
}