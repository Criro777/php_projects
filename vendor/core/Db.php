<?php

namespace vendor\core;

/**
 * Класс Db для работы с базой данных
 * @package vendor\core
 */
class Db
{
    protected static $instance;
    public $pdo;

    /**
     * Создание объекта PDO для подклоючения к БД.
     */
    protected function __construct()
    {
        $db = require ROOT . '/config/db_config.php';
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,

        ];
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']}";
        $this->pdo = new \PDO($dsn, $db['user'], $db['password'], $options);
    }

    /**
     * Создание Singleton-объекта БД
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     * Функция обёртка для создания подготовленного запроса и внесеня данных в БД
     * @param string $sql $sql текущий sql-запрос
     * @param array $params параметры запроса
     * @return bool результат выполнения запроса
     */
    public function execute($sql, $params = [], $flag = 0)
    {
        $stmt = $this->pdo->prepare($sql);
        if ($flag == 1) {

            $stmt->execute($params);
            return $id = $this->pdo->lastInsertId();

        }
        return $stmt->execute($params);
    }

    /**
     * Функция-обёртка для создания подготовленного запроса и выборки из БД
     * @param string $sql текущий sql-запрос
     * @param array $params параметры запроса
     * @return array массив с данными
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute($params);
        if (!$res) {
            throw new \PDOException();
        }
        
        return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    public function queryOne($sql, $params =[])
    {

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
        
    }

    /**
     * @param string $sql текущий sql-запрос
     * @param $key string ключ массива с данными
     * @param array $params параметры запроса
     * @return array массив с данными
     */

    public function queryEach($sql, $key, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $arr_cat = [];
        while ($row = $stmt->fetch()) {
            $arr_cat[$row[$key]] = $row;
        }
        return $arr_cat;

    }

    /**
     * @param string $sql текущий sql-запрос
     * @param array $params параметры запроса
     * @return integer количество товаров
     */

    public function queryCount($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row['count'];

    }


}