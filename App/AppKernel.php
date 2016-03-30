<?php
 use cms\HttpKernel\HttpKernel as HttpKernel;
 Class AppKernel extends HttpKernel
 {
 	public function __construct($config)
 	{
 		parent::__construct($config);
 	}

 	public function registerAlias()
 	{
 		$alias = array(
 			'Controller'=>'cms\HttpKernel\Controller',
 			'Request'=>'cms\Http\Request',
 			'View'=>'cms\Facades\View',
 			'Response'=>'cms\Http\Response'
 		);
 		parent::registerCoreAlias($alias);
 	}
 }
?>