<?php
namespace cms\HttpKernel;
class Controller
{

	public function runAction($action,$params)
	{
		$action = 'Action'.ucfirst($action);
		return call_user_func_array(array($this, $action), $params);
	}
}