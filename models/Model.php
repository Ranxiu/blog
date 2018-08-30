<?php
namespace models;

use models\Dao;

class Model{
    protected $dao;
    protected $sql;
    protected $tablename;
    function __construct(){
        $this->dao = Dao::getInstance();
        $this->sql = 'select * from '.$this->tablename;
    }

    function db_exec($sql){
        $this->dao->db_exec($sql);
        return $this;
    }

    function where($key,$par,$par2=false){
        if($par2===false){
            if(strpos($this->sql,'where')){
                $this->sql .= ' and '.$key.' = '.$par;
            }else{
                $this->sql .= ' where '.$key.' = '.$par;
            }
        }else{
            if(strpos($this->sql,'where')){
                $this->sql .= ' and '.$key.' '.$par.' '.$par2;
            }else{
                $this->sql .= ' where '.$key.' '.$par.' '.$par2;
            }
        }
        return $this;
    }

    function find(){
        return $this->dao->db_getAll($this->sql,[]);
    }
}