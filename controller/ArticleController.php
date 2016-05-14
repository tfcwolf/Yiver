<?php
 Class ArticleController extends Controller
 {
 	public function actionIndex()
 	{
 		$params1 =  Request::get('dd');
 		return View::render('back.article',array('dd'=>$params1,'b'=>2));
 	}
 }