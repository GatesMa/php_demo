<?php

namespace app\Proj;

use PDO;

// 单例模式

interface idbBase {
    static function save($db, $data);
    static function select($db, $where = []);
    static function update($db, $where = []);

    static function delete($db, $where = []);

    static function connect($dsn, $user, $password);
}


abstract class idb implements idbBase {


    private static $_instance;

    private function __construct() {
    }

    private function __clone() {
    }

    static function connect($dsn, $user, $password)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new PDO($dsn, $user, $password);
        }
        return self::$_instance;
    }

}


class DB extends idb {
    static function save($db, $data)
    {
        // TODO: Implement save() method.
    }

    /**
     * @param $db PDO
     * @param $where
     * @return mixed
     */
    static function select($db, $where = [])
    {
        // TODO: Implement select() method.
        return $db->query("SELECT * FROM class")->fetchAll(PDO::FETCH_ASSOC);
    }

    static function update($db, $where = [])
    {
        // TODO: Implement update() method.
    }

    static function delete($db, $where = [])
    {
        // TODO: Implement delete() method.
    }

}


echo '<pre>';

$config = [
  "type" => $type ?? 'mysql',
  "host" => $host ?? '9.135.235.150',
  "port" => $port ?? '3306',
  "dbName" => $dbName ?? 'boke',
  "user" => $user ?? 'root',
  "password" => $password ?? 'sTzZB*8247qjfe',
  "charset" => $charset ?? 'utf8mb4',
];

extract($config);

$dsn = sprintf('%s:host=%s;port=%s;charset=%s;dbname=%s', $type, $host, $port, $charset, $dbName);

var_dump($dsn);

$db = DB::connect($dsn, $user, $password);

var_dump(DB::select($db));

// 检查是否获取到pdo唯一实例
for ($i = 0; $i < 10; $i++) {
    var_dump(DB::connect($dsn, $username, $password));
}


































