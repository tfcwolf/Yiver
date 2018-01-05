<?php
namespace Clear;
class ClearCommand implements Command {
	public static function run() {
		$path ="data/log";
		static::delDirAndFile($path,true);
	}

	public static function  delDirAndFile($path, $delDir = FALSE) {
	    $handle = opendir($path);
	    if ($handle) {
	        while (false !== ( $item = readdir($handle) )) {
	            if ($item != "." && $item != "..")
	                is_dir("$path/$item") ? static::delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
	        }
	        closedir($handle);
	        if ($delDir)
	            return rmdir($path);
	    }else {
	        if (file_exists($path)) {
	            return unlink($path);
	        } else {
	            return FALSE;
	        }
	    }
	}
}