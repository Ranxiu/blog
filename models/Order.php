<?php
namespace models;

use PDO;

class Order extends Base {
    //下订单
    public function create($money){

        $flake = new \libs\Snowflake(1023);

        $stmt = self::$pdo->prepare('INSERT INTO orders(user_id,money,sn) VALUES(?,?,?)');

        return $stmt->execute([
            $_SESSION['id'],        
            $money,
            $flake->nextId()
        ]);

    }
    //根据编号从数据库中取出订单信息
    public function findBySn($sn){
        $stmt = self::$pdo->prepare('SELECT * FROM orders where sn=?');
        $stmt->execute([
            $sn
        ]);
        //取数据（已关联数组的结构返回数据）
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //设置订单为已支付的状态
    public function setPaid($sn){
        
        $stmt = self::$pdo->prepare("UPDATE orders SET status=1,pay_time=now() WHERE sn=?");
        return $stmt->execute([
            $sn
        ]);
    }

    // 搜索日志
    public function search()
    {
        // 设置的 $where
        $where = 'user_id='.$_SESSION['id'];

        /***************** 排序 ********************/
        // 默认排序
        $odby = 'created_at';
        $odway = 'desc';

        /****************** 翻页 ****************/
        $perpage = 15; // 每页15
        // 接收当前页码（大于等于1的整数）， max：最参数中大的值
        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        // 计算开始的下标
        // 页码  下标
        // 1 --> 0
        // 2 --> 15
        // 3 --> 30
        // 4 --> 45
        $offset = ($page-1)*$perpage;

        // 制作按钮
        // 取出总的记录数
        $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM orders WHERE $where");
        $stmt->execute();
        $count = $stmt->fetch( PDO::FETCH_COLUMN );
        // 计算总的页数（ceil：向上取整（天花板）， floor：向下取整（地板））
        $pageCount = ceil( $count / $perpage );

        $btns = '';
        for($i=1; $i<=$pageCount; $i++)
        {
            // 先获取之前的参数
            $params = getUrlParams(['page']);

            $class = $page==$i ? 'active' : '';
            $btns .= "<a class='$class' href='?{$params}page=$i'> $i </a>";
            
        }

        /*************** 执行 sqL */
        // 预处理 SQL
        $sql = "SELECT * FROM orders WHERE $where ORDER BY $odby $odway LIMIT $offset,$perpage";
        // echo $sql;
        // die();
        $stmt = self::$pdo->prepare($sql);
        // 执行 SQL
        $stmt->execute();

        // 取数据
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'btns' => $btns,
            'data' => $data,
        ];
    }

    public function startTrans(){
        self::$pdo->exec('start transaction');
    }

    public function commit(){
        self::$pdo->exec('commit');
    }

    public function rollback(){
        self::$pdo->exec('rollback');
    }
}

?>