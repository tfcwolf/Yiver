<?php
namespace cms\Db;
use \Config;
/**
 * 数据库操作类
 *
 * @copyright (c) Emlog All Rights Reserved
 */

/**
 * MYSQL数据操方法封装类
 */


class Mysql {
    private static $instance;
    private $dirty;
    private $active;
    private static $_pdo;
    private $sqls;
    private function __construct($host,$port,$driver,$dbname,$user,$pwd,$prefix)
    {
        $this->host = $host;
        $this->port= $port;
        $this->driver = $driver;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->prefix = $prefix;
        $this->dns = "$driver:host=$host;dbname=$dbname";
        $this->sql= array('query'=>"",'sqls'=>"");
    }

    public function init()
    {
        if(!$this->dirty) {
            $this->connect();
        }
    }

    public static  function getInstance()
    {
        if(empty(self::$instance)) {
            $db = Config::get('db');

            if(!empty($db)) {
                self::$instance = new Db($db['host'],$db['port'],$db['driver'],$db['dbname'],$db['user'],$db['pwd'],$db['prefix']);
                self::$instance->init();
            }  else {
                throw new DbException("数据库连接失败",1);
            }
        }
        return self::$instance;
    }

    public function buildSql()
    {
        $keymap = array(
            'select'=>'select','fields'=>'fileds','from'=>'from','where'=>'where','join'=>'left join','group'=>'group by','having'=>'having','order'=>'order by'
        );
        array_walk($keymap,array($this,"__buildSql"),$this->sql);
        return  $this->sql['sqls'];
    }

    private function __buildWhereSql($where) {
        $first = array_shift($where);

        $fsql = array_shift($first);

        foreach($where as $item) {
            $temp[] = $fsql;
            $temp[] = $item[0];
            $fsql = implode(' ' .$item['op'].' ',$temp);
        }
        return $fsql;
    }

    private function __buildSql($value,$key,$datas)
    {

        if(isset($this->sql['query'][$key])) {
            if($key == 'where') {
                $this->sql['sqls'] .=' '. $value . ' ' . $this->__buildWhereSql($this->sql['query'][$key]);
            } else {

                $this->sql['sqls'] .=' '. $value . ' ' . $this->sql['query'][$key];
            }
        }
    }


    public function query($sql)
    {
        $res = self::$_pdo->query($sql);
        
        $result = $res->fetchAll();
        return $result;
    }

    public static function insert($data,$table)
    {
        $db = DB::getInstance();
        return $db->_insert($data,$table);
    }


    public  function _insert($data,$table)
    {

        $fileds = array();
        $sets = array();
        $pvalues = array();
        $table = $this->prefix.'_'.$table;

        foreach($data as $columns=>$value) {
            $fileds[] = $columns;
            $sets[] = ':'.$columns;
            $pvalues[$columns] = "'".$value."'";
        }

        $column = implode(',',$fileds);
        $set = implode(',',$sets);

        $sql ="INSERT INTO $table($column) VALUES($set)";
        try {
            
            $stmt = self::$_pdo->prepare($sql); 
            $stmt->execute($pvalues);
        } catch (PDOException $e) {
            die ("Error!: " . $e->getMessage() . "<br/>");
        }
        return self::$_pdo->lastInsertId();
    }


    public function execute($sql)
    {
        $res = self::$_pdo->query($sql);
        return $res;
    }


    public function connect()
    {
        try {
            self::$_pdo = new PDO($this->dns, $this->user, $this->pwd);

        } catch(PDOException  $e) {
            throw new DbException("链接错误{$e->getMessage()}",1);
        }
    }

    public function close()
    {

    }

    public static function __callStatic($name, $arguments)
    {
        $db = DB::getInstance();
        //目前只支持table为静态函数
        switch ($name) {
            case 'table':
                $db->sql['query']['from'] = array_shift($arguments);
                break;
            case 'query':
        }
        return $db;
        // TODO: Implement __callStatic() method.
    }

    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'where':
                $arguments['op'] = 'and';
                $this->sql['query']['where'][] = $arguments;
                break;
            case 'select':
                $this->sql['query']['fileds'] = $arguments;
                break;
            case 'limit':
                $this->sql['query']['limit'] = $arguments;
                break;
            case 'join':
                $this->sql['query']['join'] =array_shift($arguments);
                break;
            case  'order':
                $this->sql['query']['order'] = array_shift($arguments);
                break;
            case 'andwhere':
                $arguments['op'] = 'and';
                $this->sql['query']['where'][] = $arguments;
                break;
            case 'group':
                $this->sql['query']['group'] = $arguments;
                break;
            case 'params':
                $this->sql['query']['params'][] = $arguments;
                break;
            case 'having':
                $this->sql['query']['having'] = $arguments;
                break;
        }
        return $this;
        // TODO: Implement __callStatic() method.
    }

    public function toSql()
    {
        $sql = $this->buildSql();
        return $sql;
    }
    public function get()
    {
        $this->sql['query']['select'] = "* ";

        $this->sql['query']['from'] =  $this->prefix.'_'.$this->sql['query']['from'];
        $sql = $this->buildSql();
        $result = $this->query($sql);
        return $result;
    }
}
