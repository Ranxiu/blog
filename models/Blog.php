<?php
namespace models;

use PDO;

class Blog extends Model
{   
    // 设置这个模型对应的表
    protected $table = 'blogs';
    // 设置允许接收的字段
    protected $fillable = ['display','zan_num','biaoqian','created_at','updated_at','type_id','user_id','user_name','image','is_show','content','title','jianjie'];
    // 添加、修改之前执行
    public function _before_write()
    {
        $this->_delete_logo();
        // 实现上传图片的代码
        $uploader = \libs\Uploader::make();
        $logo = '/uploads/' . $uploader->upload('image', 'blogs');
        // $this->data ：将要插入到数据库中的数据（数组）
        // 把 logo 加到数组中，就可以插入到数据库
        $this->data['image'] = $logo;    
    }
    // 删除之前被调用（钩子函数：定义好之后自动被调用）
    public function _before_delete()
    {
        $this->_delete_logo();
    }

    protected function _delete_logo()
    {
        // 如果是修改就删除原图片
        if(isset($_GET['id']))
        {
            // 先从数据库中取出原LOGO
            $ol = $this->findOne($_GET['id']);
            // 删除
            @unlink(ROOT . 'public'. $ol['logo']);
        }
    }

    //取出网站分类一级分类
    public function getType(){
        $stmt = $this->_db->prepare('SELECT * FROM types WHERE pid=0');
        $stmt->execute();
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //取出网站分类二级分类
    public function getTypes(){
        $stmt = $this->_db->prepare('SELECT * FROM types WHERE pid>0');
        $stmt->execute();
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //根据一级分类ID取出二级分类
    public function ajax_get_cat($id){
        $stmt = $this->_db->prepare('SELECT * FROM types WHERE pid=?');
        $stmt->execute([
            $id,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //取出一级分类日志
    public function getBlog($id){
        $sql = 'select * from blogs where type_id in (select id from types where pid =?)';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $id,
        ]);
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //取出二级分类日志
    public function getBlogs($id){
        $sql = 'select b.*,t.name from blogs b LEFT JOIN types t on b.type_id=t.id where b.type_id=?';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $id,
        ]);
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //获取分类名
    public function getTypename($id){
        $sql = 'select name from types where id = ?';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $id,
        ]);
        return  $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //获取日志详情页
    public function getBlogContent($id){
        $sql = 'select * from (select b.*,t.pid,t.name from blogs b LEFT JOIN types t on b.type_id=t.id) c where c.id=?';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $id,
        ]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $pid = $data['pid'];
        $sql = 'select name from types where id=?';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $pid,
        ]);
        $data1 = $stmt->fetch(PDO::FETCH_ASSOC);
        $data['fname'] = $data1['name'];
        // var_dump($data);
        return $data;
    }
    //特别推荐文章
    public function getTbtj(){
        $sql = 'select * from blogs where is_show=1';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //普通推荐文章 1大4小
    public function getPttj(){
        $sql = 'select * from blogs where is_show=2 limit 1';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $data['da'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = 'select * from blogs where is_show=2 limit 1,5';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $data['xiao'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    //点击排行文章 1大4小
    public function getDjpx(){
        $sql = 'select * from blogs order by display desc limit 1';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $data['da'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = 'select * from blogs order by display desc limit 1,5';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $data['xiao'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    //获取redis中的排行
    public function getActiveUsers(){
        $redis = \libs\Redis::getInstance();
        $data = $redis->get('active_users');
        // 转回数组（第二个参数 true:数组    false：对象）
        return json_decode($data, true);
    }
    //取出排行榜的分值 （日志表,评论表,点赞表）
    public function activeUsers(){
        //取出所有用户的日志分数
        $stmt = $this->_db->prepare('SELECT user_id,COUNT(*)*5 fz FROM blogs WHERE created_at >= DATE_SUB(CURDATE(),INTERVAL 1 year) GROUP BY user_id');
        $stmt->execute();
        $data1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //取出所有用户的评论分值
        $stmt = $this->_db->prepare('SELECT user_id,COUNT(*)*3 fz FROM comments WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 year) GROUP BY user_id');
        $stmt->execute();
        $data2 = $stmt->fetchAll( PDO::FETCH_ASSOC );

        //取出所有用户的点赞的分值
        $stmt = $this->_db->prepare('SELECT user_id,COUNT(*) fz FROM blog_zan WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 year) GROUP BY user_id');
        $stmt->execute();
        $data3 = $stmt->fetchAll( PDO::FETCH_ASSOC );

        //合并数组
        $arr = [];
        
        //合并第一个数组到空数组中
        foreach($data1 as $v){
            $arr[$v['user_id']] = $v['fz'];
        }

        //合并第二个数组到数组中
        foreach($data2 as $v){
            if(isset($arr[$v['user_id']]))
            $arr[$v['user_id']] += $v['fz'];
            else 
            $arr[$v['user_id']] = $v['fz'];
        }

        //合并第三个数组到数组中
        foreach($data3 as $v){
            if(isset($arr[$v['user_id']]))
            $arr[$v['user_id']] += $v['fz'];
            else 
            $arr[$v['user_id']] = $v['fz'];
        }

        //倒序排序
        arsort($arr);
        //取前20并保存（第四个参数保留键）
        $data = array_slice($arr,0,20,TRUE);
        
        //取出前20用户的ID
        //从数组中取出所有的键
        $userIds = array_keys($data);
        $userIds = implode(',',$userIds);
        // var_dump($userIds);
        //取出用户的头像和email
        $sql = "SELECT id,email,avatar FROM users WHERE id IN($userIds)";

        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //把计算的结果保存到Redis，因为Redis中只能保存字符串，所以我们需要把数组转成JSON字符串
        $redis = \libs\Redis::getInstance();
        $redis->set('active_users',json_encode($data));

    }

    //取出点赞过这个日志的用户信息
    public function agreeList($id){
        $sql = 'SELECT b.id,b.email,b.avatar FROM blog_zan a LEFT JOIN users b on a.user_id=b.id WHERE a.blog_id=?';
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 点赞
    public function zan($id)
    {
        // 判断是否点过
        $stmt = $this->_db->prepare('SELECT COUNT(*) FROM blog_zan WHERE user_id=? AND blog_id=?');
        $stmt->execute([
            $_SESSION['id'],
            $id
        ]);
        $count = $stmt->fetch( PDO::FETCH_COLUMN );
        if($count == 1)
        {
            return FALSE;
        }

        // 点赞
        $stmt = $this->_db->prepare("INSERT INTO blog_zan(user_id,blog_id) VALUES(?,?)");
        $ret = $stmt->execute([
            $_SESSION['id'],
            $id
        ]);

        // 更新点赞数
        if($ret)
        {
            $stmt = $this->_db->prepare('UPDATE blogs SET zan_num=zan_num+1 WHERE id=?');
            $stmt->execute([
                $id
            ]);
        }

        return $ret;
    }
    public function getNewBlog(){

        $stmt = $this->_db->prepare("SELECT * FROM blogs limit 10");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    //    var_dump($stmt->fetch(PDO::FETCH_ASSOC));
    }

    //获取最新的10个日志 供excel使用
    public function getNew(){

        $stmt = $this->_db->prepare("SELECT * FROM blogs WHERE 'user_id' = ? ");
    //    var_dump("SELECT * FROM blogs WHERE user_id = 1 ");
        $stmt->execute([
            $_SESSION['id']
        ]);
        
       return $stmt->fetchAll(PDO::FETCH_ASSOC);

    //    var_dump($stmt->fetch(PDO::FETCH_ASSOC));
    }
    // 搜索日志
    public function search()
    {
        // 设置的 $where
        $where = 'user_id='.$_SESSION['id'];

        // 放预处理对应的值
        $value = [];
        
        // 如果有keword 并值不为空时
        if(isset($_GET['keyword']) && $_GET['keyword'])
        {
            $where .= " AND (title LIKE ? OR content LIKE ?)";
            $value[] = '%'.$_GET['keyword'].'%';
            $value[] = '%'.$_GET['keyword'].'%';
        }

        if(isset($_GET['start_date']) && $_GET['start_date'])
        {
            $where .= " AND created_at >= ?";
            $value[] = $_GET['start_date'];
        }

        if(isset($_GET['end_date']) && $_GET['end_date'])
        {
            $where .= " AND created_at <= ?";
            $value[] = $_GET['end_date'];
        }

        if(isset($_GET['is_show']) && ($_GET['is_show']==1 || $_GET['is_show']==='0'))
        {
            $where .= " AND is_show = ?";
            $value[] = $_GET['is_show'];
        }


        /***************** 排序 ********************/
        // 默认排序
        $odby = 'created_at';
        $odway = 'desc';

        if(isset($_GET['odby']) && $_GET['odby'] == 'display')
        {
            $odby = 'display';
        }

        if(isset($_GET['odway']) && $_GET['odway'] == 'asc')
        {
            $odway = 'asc';
        }

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
        $stmt = $this->_db->prepare("SELECT COUNT(*) FROM blogs WHERE $where");
        $stmt->execute($value);
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
        $sql = "SELECT * FROM blogs WHERE $where ORDER BY $odby $odway LIMIT $offset,$perpage";
        // echo $sql;
        // die();
        $stmt = $this->_db->prepare($sql);
        // 执行 SQL
        $stmt->execute($value);

        // 取数据
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'btns' => $btns,
            'data' => $data,
        ];
    }
    //获取一条数据的方法
    public function find($id){
     
       
        $stmt = $this->_db->prepare("SELECT * FROM blogs WHERE id = ? ");
       
        $stmt->execute([
            $id
        ]);
        
       return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    //修改日志数据
    // public function update()
    // {   

    //     $blog = new Blog;
    //     $sql = "UPDATE blogs SET title= '{$title}',content= '{$content}',is_show={$is_show} WHERE id={$id}";
    //     // echo $sql;
    //     if($is_show == 1)
    //     {
    //         $blog->makeHtml($id);
    //     }
    //     else
    //     {
    //         // 如果改为私有，就要将原来的静态页删除掉
    //         $blog->deleteHtml($id);
    //     }

    //     return $stmt = $this->_db->exec($sql);
    //     // return $stmt->execute([
    //     //     $title,
    //     //     $content,
    //     //     $is_show,
    //     //     $id,
    //     // ]);
    //         // echo $sql;
    //     // var_dump($title,$content,$is_show,$id);
    // }

    //为某一个日志生成静态页面
        //参数：日志的id
        public function makeHtml($id){
            //1.取出日志的信息
            $blog = $this->find($id);

            //2.打开缓冲区、并且加载视图到缓冲区
            ob_start();
            view('blogs.content',[
                'blog' => $blog,
            ]);

            //3.从缓冲区取出视图并写到静态页中
            $str = ob_get_clean();

            file_put_contents(ROOT.'public/contents/'.$id.'.html',$str);
        }
    //删除静态页
    public function deleteHtml($id){
        //@防止 报错： 有这个文件就会删除，没有就不删除，不用报错
        @unlink(ROOT.'public/contents/'.$id.'.html');
    }

    public function content2html()
    {
        $stmt = $this->_db->query('SELECT * FROM blogs');
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
       
        // die();
      
        // 生成静态页
        foreach($blogs as $v)
        {   
            // 开启缓冲区
            ob_start();
            // 加载视图
            // echo "<pre>";
            // var_dump($v);
           view('blogs.content', [
                'blog' => $v,
            ]);           
            // // 取出缓冲区的内容
            $str = ob_get_contents();
           
            // var_dump($str);
            // // // 生成静态页
            file_put_contents(ROOT.'public/contents/'.$v['id'].'.html', $str);
            // // 清空缓冲区
            ob_clean();
        }
    }

    public function index2html()
    {
        // 取 前5 条记录 数据 
        $stmt = $this->_db->query("SELECT * FROM blogs WHERE is_show=1 ORDER BY id DESC LIMIT 10");
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);   
        
        // 开启一个缓冲区
        ob_start();

        // 加载视图文件到缓冲区
        view('index.index', [
            'blogs' => $blogs,
        ]);

        // 从缓冲区中取出页面
        $str = ob_get_contents();

        // 把页面的内容生成到一个静态页中
        file_put_contents(ROOT.'public/index.html', $str);

    }
    //删除日志
    public function delete($id){
        //只能删除自己的日志
        $stmt = $this->_db->prepare('DELETE FROM blogs WHERE id = ? AND user_id = ?');
        $blog->deleteHtml($id);
        $stmt->execute([
            $id,
            $_SESSION['id'],
        ]);
    }

    
    // 获取日志的浏览量
    // 参数：日志ID
    public function getDisplay($id)
    {
        // 使用日志ID拼出键名
        $key = "blog-{$id}";

        // 连接 Redis
        $redis = \libs\Redis::getInstance();

        //普通set/get操作
       

        // 判断 hash 中是否有这个键，如果有就操作内存，如果没有就从数据库中取
        // hexists：判断有没有键
        if($redis->hexists('blog_displays', $key))
        {
            // 累加 并且 返回添加完之后的值
            // hincrby ：把值加1
            $newNum = $redis->hincrby('blog_displays', $key, 1);
            return $newNum;
        }
        else
        {
            // 从数据库中取出浏览量
            $stmt = $this->_db->prepare('SELECT display FROM blogs WHERE id=?');
            $stmt->execute([$id]);
            $display = $stmt->fetch( PDO::FETCH_COLUMN );
            $display++;
            // 保存到 redis
            // hset：保存到  Redis
            $redis->hset('blog_displays', $key, $display);
            return $display;
        }
    }

    // 把内存中的浏览量回写到数据库中
    public function displayToDb()
    {
        // 1. 先取出内存中所有的浏览量
        // 连接 Redis
        $redis = \libs\Redis::getInstance();

        $data = $redis->hgetall('blog_displays');

        // 2. 更新回数据库
        foreach($data as $k => $v)
        {
            $id = str_replace('blog-', '', $k);
            $sql = "UPDATE blogs SET display={$v} WHERE id = {$id}";
            $this->_db->exec($sql);
        }
    }
    //添加日志表单提交
    public function add($title,$content,$is_show){
        $date = time();
        $date = date('Y-m-d H:i:s', $date);
       
        // 如果日志是公开的就生成静态页
        if($is_show == 1)
        {
            $blog->makeHtml($id);
        }
        
        $stmt = $this->_db->prepare("INSERT INTO blogs(title,content,is_show,user_id,created_at) VALUES(?,?,?,?,?)");
        $ret = $stmt->execute([
            $title,
            $content,
            $is_show,
            $_SESSION['id'],
            $date,
        ]);
        // var_dump( $title, $content, $is_show,$_SESSION['id'],$date);
        // die();
        if(!$ret)
        {
            echo '失败';
            // 获取失败信息
            $error = $stmt->errorInfo();
            echo '<pre>';
            var_dump( $error); 
            exit;
        }
        // 返回新插入的记录的ID
        return $this->_db->lastInsertId();
    }
    
    // 添加、修改之后执行
    public function _after_write()
    {
        /**
         * 在我这个框架中，
         * 通过 $this->data['id']：获取新添加的记录的ID
         */

        // var_dump( $_FILES );
        // exit;

        /**
         * 处理商品属性
         */

        $stmt = $this->_db->prepare("INSERT INTO goods_attribute
                        (attr_name,attr_value,goods_id) VALUES(?,?,?)");
        // 循环每一个属性，插入到属性表
        foreach($_POST['attr_name'] as $k => $v)
        {
            /**
             * INSERT INTO goods_attribute
             * (attr_name,attr_value,goods_id) 
             *       VALUES(?,?,?)
             */
            $stmt->execute([
                $v,
                $_POST['attr_value'][$k],
                $this->data['id'],
            ]);
        }

        /**
          * 商品图片
          */
        $uploader = \libs\Uploader::make();

        $stmt = $this->_db->prepare("INSERT INTO goods_image(goods_id,path) VALUES(?,?)");
        $_tmp = [];
        // 循环图片
        foreach($_FILES['image']['name'] as $k => $v)
        {
            // 拼出每张图片需要的数组
            $_tmp['name'] = $v;
            $_tmp['type'] = $_FILES['image']['type'][$k];
            $_tmp['tmp_name'] = $_FILES['image']['tmp_name'][$k];
            $_tmp['error'] = $_FILES['image']['error'][$k];
            $_tmp['size'] = $_FILES['image']['size'][$k];

            // 放到 $_FILES 数组中
            $_FILES['tmp'] = $_tmp;

            // upload 这个类会到 $_FILES 中去找图片
            // 参数一、就代表图片在 $_FILES 数组中的名字
            // upload 方法现在就可以直接到 $_FILES 中去找到 tmp 来上传了
            $path = '/uploads/'.$uploader->upload('tmp', 'goods');

            // 执行SQL
            $stmt->execute([
                $this->data['id'],
                $path,
            ]);

        }

        
        
        /**
           * SKU
           */
        $stmt = $this->_db->prepare("INSERT INTO goods_sku
                (goods_id,sku_name,stock,price) VALUES(?,?,?,?)");

        foreach($_POST['sku_name'] as $k => $v)
        {
            $stmt->execute([
                $this->data['id'],
                $v,
                $_POST['stock'][$k],
                $_POST['price'][$k],
            ]);
        } 


    }
}