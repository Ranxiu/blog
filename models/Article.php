<?php
namespace models;

use models\Model;

class Article extends Model
{
    public $tableName = 'articles';

    public function getAll()
    {   
        $sql = "select * from ".$this->tableName;

        // echo $sql;

        return $this->find($sql);
    }
}