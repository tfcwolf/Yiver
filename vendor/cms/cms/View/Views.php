<?php
namespace cms\View;
use cms\HttpKernel\HttpKernel;
class Views{
	private $factory;
	public function __construct()
	{
		$basePath = HttpKernel::getBasePath();
		$path = [$basePath.'/view'];         // your view file path, it's an array
		$cachePath = $basePath.'/runtime/cache/blade';     // compiled file path
		if (!file_exists($cachePath)) { // 判断存放文件目录是否存在
            mkdir($cachePath, 0777, true);
        }
		$compiler = new Blade\Compilers\BladeCompiler($cachePath);

		// you can add a custom directive if you want
		$compiler->directive('datetime', function($timestamp) {
		    return preg_replace('/(\(\d+\))/', '<?php echo date("Y-m-d H:i:s", $1); ?>', $timestamp);
		});

		$engine = new Blade\Engines\CompilerEngine($compiler);
		$finder = new Blade\FileViewFinder($path);

		// if your view file extension is not php or blade.php, use this to add it
		//$finder->addExtension('php');

		// get an instance of factory
		$this->factory = new Blade\Factory($engine, $finder);
		$this->factory->share('app',HttpKernel::$kernel);

		// render the template file and echo it
	}

	public function render($tpl,$params)
	{
	   return $this->factory->make($tpl, $params)->render();
	}

	public function boot()
	{
		
	}
}