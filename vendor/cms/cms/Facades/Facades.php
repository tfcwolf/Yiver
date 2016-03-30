<?php
 namespace cms\Facades;
 class Facades
 {
 	
 	public static function getAccessName()
 	{

 	}

 	public static function resolveClass()
 	{
 		return static::$app[$name];
 	}

 	public static function __callStatic($method,$args)
 	{
 		$instance = static::getAccessName();
 		
 		$instance = new $instance();
 		//$instance = new \cms\View\Views();
 		//var_dump(array($instance, $method));exit;
 		return call_user_func_array(array($instance, $method), $args);	
 	}
 }