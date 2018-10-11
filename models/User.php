<?php
namespace models;

use PDO;

class User extends Model
{   
    //获取该用户注册账号时的时间
    public function getTime(){
        $stmt = $this->_db->prepare('SELECT created_at FROM users WHERE id = ?');
        $stmt->execute([
            $_SESSION['id'],
        ]);
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    //获取所有用户
    public function getAll(){
        $stmt = $this->_db->query('SELECT * FROM users');

        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //设置头像
    public function setAvatar($path)
    {
        $stmt = $this->_db->prepare('UPDATE users SET avatar=? WHERE id=?');
        $stmt->execute([
            $path,
            $_SESSION['id']
        ]);
    }
    //检查账号是否已经存在
    public function isexits($email){
        $stmt = $this->_db->prepare('SELECT id from users where email=?');
        return $stmt->execute([
            $email,
        ]);
    }

    //注册账号
    public function add($email,$password)
    {
        $stmt = $this->_db->prepare("INSERT INTO users (email,password) VALUES(?,?)");
        return $stmt->execute([
                                $email,
                                $password,
                            ]);
    }

    public function login($email,$password)
    {
        $stmt = $this->_db->prepare("SELECT * FROM users WHERE email=? AND password=?");
        $stmt->execute([
            $email,
            $password,
        ]);

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if($user){
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    // 为用户增加金额
    public function addMoney($money, $userId)
    {
        $stmt = $this->_db->prepare("UPDATE users SET money=money+? WHERE id=?");
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
        $stmt = $this->_db->prepare('SELECT money FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $money = $stmt->fetch( PDO::FETCH_COLUMN );
        // 更新到SESSION中
        $_SESSION['money'] = $money;
        
        return $money;
        
    }
}