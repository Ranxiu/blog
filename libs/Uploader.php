<?php
namespace libs;

class Uploader {
    //不允许被new 来生成对象
    private function __construct(){}
    //不允许克隆
    private function __clone(){}
    //保存唯一的对象（只有静态的属性属于这个类是唯一的）
    private static $_uploader = null;

    //提供一个公开的方法获取对象
    public static function make(){

        if(self::$_uploader===null){

            self::$_uploader = new self;
        }
        return self::$_uploader;
    }

    private $_root = ROOT.'public/uploads/';  //图片保存的根路径
    private $_ext = ['image/jpeg','image/jpg','image/ejpeg','image/png','image/gif','image/bmp'];
    private $_maxSize = 1024*1024*1.8; // 最大允许上传的尺寸 1.8M

    private $_file; //保存用户上传的图片信息
    private $_subDir; //保存二级目录

    //上传图片
    //参数一、 表单中文件名
    //参数二、保存到的二级目录
    public function upload($name,$subdir){

        //把用户图片的信息保存到属性上
        $this->_file = $_FILES[$name];
        $this->_subDir = $subdir;

        if(!$this->_checkType()){
            die('图片类型不正确！');
        }

        if(!$this->_checkSize()){
            die('图片尺寸不正确');
        }

        //创建目录
        $dir = $this->_makeDir();
        //生成唯一的名字
        $name = $this->_makeName();
        //移动图片
        move_uploaded_file($this->_file['tmp_name'],$this->_root.$dir.$name);

        //返回二级目录开始的路径
        return $dir.$name; 
    }

    //创建目录
    private function _makeDir(){

        $dir = $this->_subDir.'/'. date("Ymd").'/';

        //根据当天日期创建目录（如果目录不存在） 
        if(!is_dir($this->_root.$dir)){

            mkdir($this->_root.$dir,0777,TRUE);
        }

        return $dir;
    }

    //生成唯一的图片名称
    private function _makeName(){
        $name = md5(time().rand(1,9999));
        $ext = strrchr($this->_file['name'],'.');
        return $name.$ext;
    }

    //检测图片类型
    private function _checkType(){
        return in_array($this->_file['type'],$this->_ext);
    }

    private function _checkSize(){
        return $this->_file['size'] < $this->_maxSize;
    }



    //批量上传
    public function uploadall($subdir){
        
        $this->_subDir = $subdir;
        //创建目录
        $dir = $this->_makeDir();
        //循环多张图片的name
        foreach($_FILES['images']['name'] as $k => $v){
            $name = md5( time() . rand(1,9999) );
            $ext = strrchr($v, '.');    // .jpg
            $name = $name . $ext;
            // 根据 name 的下标找到对应的临时文件并移动
            move_uploaded_file($_FILES['images']['tmp_name'][$k], $this->_root.$dir.$name);
            echo $this->_root.$dir.$name. '<hr>';
            // var_dump( $this->_root.$dir.$name);
        }
    }
}
?>