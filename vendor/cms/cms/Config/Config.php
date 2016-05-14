<?php
namespace cms\Config;
class Config {
	static private $config;
	static public function get($name)
	{
		return self::$config[$name];
	}

	static public function set($key,$name)
	{
		return self::$config[$key] = $name;
	}

	static public function load($file){
		self::$config = include_once $file;
	}
}