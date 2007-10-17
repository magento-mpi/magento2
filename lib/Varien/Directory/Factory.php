<?php
require_once("Varien/Directory/Collection.php");
require_once("Varien/File/Object.php");

class Varien_Directory_Factory{

	static public function getFactory($path,$is_recursion = true,$recurse_level=0){
		if(is_dir($path)){
			$obj = new Varien_Directory_Collection($path,$is_recursion,$recurse_level+1);
			return $obj;
		} else {
			return new Varien_File_Object($path);
		}
	}
	
}
?>