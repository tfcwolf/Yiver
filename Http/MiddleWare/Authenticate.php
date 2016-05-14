<?php
namespace App\Http\MiddleWare;
class Authenticate{

	public function handle()
	{
		if(!\App\App\LoginAuth::isLogin()) {
			if(!strpos(\Request::server('REQUEST_URI'),'Login'))
			{
				$url = \Url::create('Login/Index');
				\Response::redirect($url);
			}
		}
	}
}