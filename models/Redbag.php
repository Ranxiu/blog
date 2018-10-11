<?php
namespace models;

use PDO;

class Redbag extends Model {

    public function create($userId){
        $stmt = self::$pdo->prepare('INSERT INTO redbags(user_id) VALUEs(?)');
        $stmt->execute([$userId]);
    }
}

?>