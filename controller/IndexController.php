<?php
 use App\model\User as User;
 Class IndexController extends Controller
 {
 	public function actionIndex()
 	{
 		User::model()->findAll();
 		$params1 =  Request::get('dd');
 		return View::render('back.index',array('dd'=>$params1,'b'=>2));
 	}
 }