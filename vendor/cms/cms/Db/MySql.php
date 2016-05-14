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

class MySql {

	/**
	 * 查询次数
	 * @var int
	 */
	static private $params;
	private $queryCount = 0;

	/**
	 * 内部数据连接对象
	 * @var resourse
	 */
	private $conn;

	/**
	 * 内部数据结果
	 * @var resourse
	 */
	private $result;

	/**
	 * 内部实例对象
	 * @var object MySql
	 */
	private static $instance = null;

	/**
	 * 构造函数
	 */
	private function __construct($params) {
		if (!function_exists('mysql_connect')) {
			emMsg('服务器PHP不支持MySql数据库');
		}
		if (!$this->conn = @mysql_connect($params['host'], $params['user'], $params['password'])) {
            switch ($this->geterrno()) {
                case 2005:
                    emMsg("连接数据库失败，数据库地址错误或者数据库服务器不可用");
                    break;
                case 2003:
                    emMsg("连接数据库失败，数据库端口错误");
                    break;
                case 2006:
                    emMsg("连接数据库失败，数据库服务器不可用");
                    break;
                case 1045:
                    emMsg("连接数据库失败，数据库用户名或密码错误");
                    break;
                default :
                    emMsg("连接数据库失败，请检查数据库信息。错误编号：" . $this->geterrno());
                    break;
            }
		}
		if ($this->getMysqlVersion() > '4.1') {
			mysql_query("SET NAMES 'utf8'");
		}
		@mysql_select_db($params['dbname'], $this->conn) OR emMsg("连接数据库失败，未找到您填写的数据库");
	}

	/**
	 * 静态方法，返回数据库连接实例
	 */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$params = Config::get('db');
			self::$instance = new MySql(self::$params);
		}
		return self::$instance;
	}

	/**
	 * 关闭数据库连接
	 */
	function close() {
		return mysql_close($this->conn);
	}

	/**
	 * 发送查询语句
	 */
	function query($sql) {
		$preg = self::$params['prefix'].'$1';
		$sql =preg_replace("/\{\{(.*?)\}\}/",$preg,$sql);
		$this->result = @mysql_query($sql, $this->conn);
		$this->queryCount++;
		if (!$this->result) {
			emMsg("SQL语句执行错误：$sql <br />" . $this->geterror().' '.$sql);
		}else {
			return $this->result;
		}
	}

	/**
	 * 从结果集中取得一行作为关联数组/数字索引数组
	 *
	 */
	function fetch_array($query , $type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $type);
	}

	function once_fetch_array($sql) {
		$this->result = $this->query($sql);
		return $this->fetch_array($this->result);
	}

	/**
	 * 从结果集中取得一行作为数字索引数组
	 *
	 */
	function fetch_row($query) {
		return mysql_fetch_row($query);
	}

	/**
	 * 取得行的数目
	 *
	 */
	function num_rows($query) {
		return mysql_num_rows($query);
	}

	/**
	 * 取得结果集中字段的数目
	 */
	function num_fields($query) {
		return mysql_num_fields($query);
	}
	/**
	 * 取得上一步 INSERT 操作产生的 ID
	 */
	function insert_id() {
		return mysql_insert_id($this->conn);
	}

	/**
	 * 获取mysql错误
	 */
	function geterror() {
		return mysql_error();
	}

    /**
	 * 获取mysql错误编码
	 */
	function geterrno() {
		return mysql_errno();
	}

	/**
	 * Get number of affected rows in previous MySQL operation
	 */
	function affected_rows() {
		return mysql_affected_rows();
	}

	/**
	 * 取得数据库版本信息
	 */
	function getMysqlVersion() {
		return mysql_get_server_info();
	}

	/**
	 * 取得数据库查询次数
	 */
	function getQueryCount() {
		return $this->queryCount;
	}
}
