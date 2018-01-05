<?php 
/**
 * 命令行交互
 */
class Console {
	private $params=array('sopt'=>'','lopt'=>array());
	private $is_runing = true;

	public function run(){
		while ($this->is_runing) {
			$command = $this->input();
			//$command = $this->parse($command);
			$this->is_runing = $this->execute($command);
			
		}
		exit;
	}

	public function init() {
		//getopt($this->params['sopt'],$this->params['lopt']);
		spl_autoload_register(array($this,'autoload'));
	}
	
	public function parse($command) {
		$command = trim($command);
		$commands = explode(' ',$command);
		if(isset($command[0])) {
			//是否包含子命令
			if(strpos($command[0],':')) {
				$tcmd = explode(':',$command[0]);
				$cmd = array('cmd'=>$tcmd[0],'subcmd'=>$tcmd[1]);
			} else {
				$cmd = array('cmd'=>$command[0]);
			}

		}
		return $command;
	}

	public function execute($command) {

		foreach($this->task as $item) {
			if($item['name'] == $command) {   
				return $item['exec']($command,$this);
			}
		}
		if($command == 'quit') {
			return false;
		}
		return true;
	}

	public function autoload($class) {
		
		include $class.".php";
	}

	public function input($str = "请输入:\n")  
	{  
	    //提示输入  
	    $str = iconv('UTF-8','GBK', $str);
	    fwrite(STDOUT, $str );  
	    //获取用户输入数据  
	    $result = trim(fgets(STDIN));  
	    return trim($result);  
	} 

	public function output($outStr) {
		$outStr = iconv('UTF-8','GBK', $outStr);
		fwrite(STDOUT,$outStr);
	}

	public function loadCommand($name,$function) {
		$this->task[]= array('name'=>$name,'exec'=>$function);
	}

	public function println($str) {
		$this->output($str."\n");
	}

	public function showUI() {
		$this->println("Tools version:0.0.1");
		$this->println("");
		$this->println("Usage:");
		$this->println("  command [option] [arguments]");
		$this->println("Available commands:");
		$this->println("  clear");
		$this->println("  migrate");
		$this->println("  make");
		$this->println("  list");
		$this->println("  push");
		$this->println("  svn:push");
		$this->println("  svn:roll");
		$this->println("  quit");
		$this->println("\t");
	}
}

$cli = new Console();
$cli->init();
$cli->showUI();

$cli->loadCommand('show',function($cmd,$context){
	$context->println("show this code");
	return false;
});

include "route.php";
$cli->run();