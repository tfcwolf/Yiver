<?php
 namespace cms\HttpKernel;
 use cms\Container\Container as Container;
 use Exception;
 class HttpKernel extends Container{
 	private $basePath;
 	private static $s;
 	public static $kernel;
 	public function __construct($config)
 	{
 		$this->registerAlias();
 		self::$kernel = $this;
 		$this->setBaseUrl($config['host']);
 		$this->setBasePath($config['basePath']);
 	}

 	public  function setBaseUrl($host)
 	{
 		$this->baseUrl = $host;
 	}

 	public  function getbaseUrl()
 	{
 		return $this->baseUrl;
 	}

 	public function setBasePath($path)
 	{
 		self::$s['basePath'] = $path;
 	}

 	public function registerCoreAlias($classmap)
 	{
 		foreach($classmap as $key=>$class){
 			class_alias($class,$key);
 		}
 	}

 	public static function getControllerPath()
 	{
 		return '\\controller\\';
 	}

 	public static function getBasePath()
 	{
 		return self::$s['basePath'];
 	}

 	public function handle($request)
 	{

 		$response = new \Response();
 		try{
 		$route = $this->make('route');
 		$route->load("route.php");
 		$routeinfo = $route->Dispatch($request);
 		if($routeinfo){
 			$dispatchinfo = $this->DispatchController($routeinfo);
 		} else {
 			$routeinfo = explode('/',$request->relativeUri());
 			array_shift($routeinfo);
 			$controller = $this->createController($routeinfo[0]);
 			if(!$controller) {
 				throw new Exception("无法找到匹配的路由或控制器",500);
 			}
 			$action = $routeinfo[1];
 			$params = $this->parseParams($routeinfo);
 			$request->GetsMerge($params);
 			if(!is_callable(array($controller,$action))) {
 				throw new Exception("无法找到控制器对应的方法",500);
 			}
 			$content = $controller->runAction($action,$params);
 			$code = 200;
 			//$list('controller','action') = $routeinfo;
 		}
 		}catch(Exception $e){
 			$response->setCode($e->getCode());
 			$response->setBody($e->getMessage());
 			return $response;
 		}

 		$response->setCode($code);
 		$response->setBody($content);
 		return $response;
 	}

 	public function DispatchController($params)
 	{
 		$this->createController($params[0]);
 	}

 	public function RunController($controller,$routeinfo)
 	{
 		$params = $this->parseParams($routeinfo);
 	}

 	public function parseParams($routeinfo)
 	{
 		array_shift($routeinfo);
 		array_shift($routeinfo);
 		$length = count($routeinfo);
 		for($i=0;$i<$length;$i=$i+2){
 			$params[$routeinfo[$i]]= $routeinfo[$i+1];
 		}
 		return $params;
 	} 	

 	public  function createController($id)
 	{
 		$basePath = static::getBasePath().static::getControllerPath();
 		$className=ucfirst($id).'Controller';
		$classFile=$basePath.DIRECTORY_SEPARATOR.$className.'.php';
		if(is_file($classFile))
		{
			if(!class_exists($className,false))
				require($classFile);
			if(class_exists($className,false))
			{
				//
				return
					new $className();
					//$this->parseActionParams($route)
			}
			return null;
		}
 	}
 }
?>