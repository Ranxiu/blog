<?php
namespace models;

use models\Dao;

class Model{
    public $dao;
    public $tablename;
    function __construct(){
        $this->dao = Dao::getInstance();
    }

    // function db_exec($sql){
    //     $this->dao->db_exec($sql);
    //     return $this;
    // }
    function find($sql,$data=[]){
        return $this->dao->db_getAll($sql,$data);
    }
    // function where($key,$par,$par2=false){
    //     if($par2===false){
    //         if(strpos($this->sql,'where')){
    //             $this->sql .= ' and '.$key.' = '.$par;
    //         }else{
    //             $this->sql .= ' where '.$key.' = '.$par;
    //         }
    //     }else{
    //         if(strpos($this->sql,'where')){
    //             $this->sql .= ' and '.$key.' '.$par.' '.$par2;
    //         }else{
    //             $this->sql .= ' where '.$key.' '.$par.' '.$par2;
    //         }
    //     }
    //     return $this;
    // }

    
}