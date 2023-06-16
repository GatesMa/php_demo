<?php

/**\
 * 事件委托 请求委托  访问类中不存在的成远方法，会被魔术方法拦截，将请求重定向给或者委托给别的对象成员方法类处理。
 * 委托是指一个对象转发或者委托一个请求给另一个对象，被委托的一方替原先对象处理请求。
 * 委托比继承更加灵活  父类与子类的关系是固定的，只能单继承，但是请求可以委托给多个对象
 */

//  被委托的类   数据库查询构造器
class Query
{
    //创建pdo对象的唯一实例
    private static $db;
    protected $table;
    protected $field;
    protected $limit;


    // private私有的 阻止此类在外部进行实例化
    private function __construct()
    {
    }


    static function connect($dsn, $user, $pwd)
    {
        if (is_null(static::$db)) {
            static::$db = new pdo($dsn, $user, $pwd);
        }
        // return static::$db;
        // 返回query实例
        return new static;
    }


    public function table($table)
    {
        $this->table = $table;
        return $this; //返回本对象 为了实现链式调用
    }


    public function where($where)
    {
    }


    public function field($field)
    {
        $this->field = $field;
        return $this;
    }


    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }


    public function getSql()
    {
        return sprintf('SELECT %s FROM %s LIMIT %d ', $this->field, $this->table, $this->limit);
    }
    public function select()
    {
        return  static::$db->query($this->getSql())->fetchAll(PDO::FETCH_ASSOC);
    }
}



// 委托方

class Db
{
    static function __callStatic($name, $arguments)
    {
        $dsn = 'mysql:host=localhost;dbname=phpcn;charset=utf8;port=3308';
        $user = 'root';
        $pwd = '';

        // 获取到被委托的类Query实例
        $q = Query::connect($dsn, $user, $pwd);
        return  call_user_func([$q, $name], ...$arguments);
    }
}



// 客户端代码
$res = Db::table('mj_course_cat')->field('cat_id,name')->limit(20)->select();
var_dump($res);
