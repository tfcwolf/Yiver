<?php
 Class SiteController extends Controller
 {
 	public function actionIndex()
 	{
 		$params1 =  Request::get('dd');
 		return View::render('hello',array('dd'=>$params1,'b'=>2));
 	}
 }