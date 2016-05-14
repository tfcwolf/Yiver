<?php
 Class TestController extends Controller
 {
 	public function actionShow()
 	{
 		$params1 =  Request::get('dd');
 		return View::render('hello',array('dd'=>$params1,'b'=>2));
 	}
 }