<?php
namespace models;

use models\Model;

class Article extends Model
{
    protected $tableName = 'articles';

    public function getName()
    {
        return 'tom';
    }
}