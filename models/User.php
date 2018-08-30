<?php
namespace models;

use models\Model;

class User extends Model
{
    protected $tablename = 'articles';
    public function getName()
    {
        return 'tom';
    }
    private function getChar($num)  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<$num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }

    public function articles()
    {
        // $this->db_exec('SET NAMES utf8')->db_exec('TRUNCATE articles');
        echo "<pre>";
        var_dump($this->where('id','>',50)->where('id','<',60)->find());

        // for($i=0;$i<100;$i++)
        // {
        //     $title = $this->getChar( rand(20,100) ) ;
        //     $content = $this->getChar( rand(100,600) );
        //     $read_num = rand(10,500);
        //     $zan_num = rand(10,500);
        //     $date = rand(1233333399,1535592288);
        //     $date = date('Y-m-d H:i:s', $date);
        //     $this->db_exec("INSERT INTO articles (title,content,read_num,zan_num,created_at) VALUES('$title','$content',$read_num,$zan_num,'$date')",$data=[]);
        // }
    }
}