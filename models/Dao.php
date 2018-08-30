<?php
namespace models;

use PDO;
use PDOException;

class Dao
{
    
        private static $dao = null;

		public static $error="";
		
		public $pdo;

		private function __construct(){

			//配置信息
			$type = 'mysql';  
			$host = 'localhost';
			$port = 3306;
			$user = 'root';
			$pass = '573511';
			$dbname = 'blog';
			$charset = 'utf8';

			try {
				//实例化数据库对象
				$this->pdo = new PDO("{$type}:host={$host};port={$port};charset={$charset};dbname={$dbname}",$user,$pass);
				//设置编码格式
				$this->pdo->exec("set names {$charset}");
				//设置PDO错误信息显示
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

			}catch(PDOException $e){

				//保存错误信息到静态属性中
				self::$error = $e->getMessage();
			}


		}

		private function __clone(){}

		//获取单例对象的静态方法
		public static function getInstance(){

			//判断对象是否是第一次创建，如果是第一次，new 对象
			if(!self::$dao instanceof self){
				//产生一个对象，并且放到 $dao对象中
				self::$dao = new self;
			}

			//如果不是第一次，直接返回静态属性
			return self::$dao;
		}


		// $_POST['name'] = 'admin';
		// $_POST['password']  = '123';

		// $data = [
		// 	'name'=>'admin',
		// 	'password'=>'123'
		// ]
				

		// $sql = 'select * from user where name=:name and password=:password';



		//定义方法，查询所有数据
		function db_getAll($sql,$data){

			try{
				//预处理
				$stmt = $this->pdo->prepare($sql);
				//如果预处理成功，则绑定数据
				if($stmt->execute($data)){
					return $stmt->fetchAll(PDO::FETCH_ASSOC);
				}

			}catch(PDOException $e){
				//保存错误信息
				self::$error = $e->getMessage();
				return false;
			}

		}

	
		function db_exec($sql,$data=[]){

			try{
				//预处理
				$stmt = $this->pdo->prepare($sql);
				//如果预处理成功，则绑定数据				
				return $stmt->execute($data);
			}catch(PDOException $e){
				//保存错误信息
				self::$error = $e->getMessage();
				return false;
			}


		}
		//返回插入成功后的记录的id
		function db_lastInsertId(){
			return $this->pdo->lastInsertId();
		}

		//获取一行
		function db_getRow($sql,$data){

			try{
				//预处理
				$stmt = $this->pdo->prepare($sql);
				//如果预处理成功，则绑定数据
				if($stmt->execute($data)){
					 return $stmt->fetch(PDO::FETCH_ASSOC);
				}

			}catch(PDOException $e){
				//保存错误信息
				self::$error = $e->getMessage();
				return false;
			}

		}

		//获取一列
		function db_getFirstField($sql,$data){

			try{
				//预处理
				$stmt = $this->pdo->prepare($sql);
				//如果预处理成功，则绑定数据
				if($stmt->execute($data)){
					$arr = $stmt->fetch(PDO::FETCH_NUM);
					return $arr[0];
				}

			}catch(PDOException $e){
				//保存错误信息
				self::$error = $e->getMessage();
				return false;
			}

		}
}