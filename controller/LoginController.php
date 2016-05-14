<?php
 use App\model\User as User;
 Class LoginController extends Controller
 {
 	public function actionIndex()
 	{
 		User::model()->findAll();
 		$params1 =  Request::get('dd');
 		$showData['url'] = Url::create('Login/check');

 		return View::render('back.login',$showData);
 	}

 	public function actionCheck()
 	{
 		$username = Request::post('username');
 		$password = Request::post('password');
 		if($res = App\App\LoginAuth::checkUser($username,$password)){
 			if(is_array($res)){
 				$id = $res['id'];
 				App\App\LoginAuth::setAuthCookie($id,$res['username']);
 				$url = \Url::create('Index/Index');
				\Response::redirect($url);
 			}
 		}
 	}
 }