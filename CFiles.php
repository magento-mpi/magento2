<?php
class CFiles {
	
	
	public static function readpath($dir,&$dirs,&$files){
		$dp=opendir($dir);
		while (false!=($file=readdir($dp))){
			if ($file!="." && $file!=".." && $file[0]!="."){
				if (is_dir($dir.$file))	{
					self::readpath($dir.$file."/",$dirs,$files); // uses recursion
					$dirs[] = $dir.$file;  // reads the dir into an array
				} else {
					$files[] = $dir.$file; // reads the file into an array
				}
			}
		}
	}
	
	public static function getExt($path) {
		
		$path_parts = pathinfo($path);
		if(isset($path_parts["extension"]))
			return $path_parts["extension"];
		else
			return ''; 
	}
	
}



?>