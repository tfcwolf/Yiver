<?php

$cli->loadCommand('clear',function($cmd,$context){
	$class = $cmd."\\".ucfirst($cmd).'Command';
	$class::run();
	$context->println("clearup");
	return false;
});

$cli->loadCommand('svn:push',function($cmd,$context){
	$class = $cmd."\\".ucfirst($cmd).'Command';
	$class::run();
	$context->println("clearup");
	return false;
});

$cli->loadCommand('svn:roll',function($cmd,$context){
	$class = $cmd."\\".ucfirst($cmd).'Command';
	$class::run();
	$context->println("clearup");
	return false;
});


$cli->loadCommand('show',function($cmd,$context){
	$context->println("show this code");
	return false;
});
