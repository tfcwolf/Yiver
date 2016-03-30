<?php 
  include "vendor/autoload.php";
  include "App\AppKernel.php";
  include "vendor\cms\cms\Start.php";
  $config = array("basePath"=>dirname(__FILE__),'host'=>'http://localhost:8880/test/');
  $kernel = new AppKernel($config);
  $kernel->singleton('route','cms\route\Route');
  $kernel->singleton('request','cms\Http\Request');
  //$route = new Route();
  $request = $kernel->make('request');
  $response = $kernel->handle($request);
  $response->send();
  exit;
?>