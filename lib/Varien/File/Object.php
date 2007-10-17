<?php

require_once("Varien/Object.php");
require_once('Varien/Directory/IFactory.php');

class Varien_File_Object extends SplFileObject implements IFactory {
	protected $_filename;
	protected $_path;
	protected $_filter;
	protected $_isCorrect=true; # - pass or not filter checking
	
	protected $filtered;
	
	public function __construct($path)
	{
		parent::__construct($path);
		$this->_path=$path;
		$this->_filename=basename($path);
	}
	
	public function getFilesName(&$files)
	{
		$this->getFileName(&$files);
	}
	
	public function getFileName(&$files='')
	{
		if($this->_isCorrect){
			if($files=='')
				return $this->_filename;
			$files[] = $this->_filename;
		}
	}
	
	public function getFilesPaths(&$paths)
	{
		if($this->_isCorrect){
			$paths[] = (string)$this->_path;
		}
	}
	
	public function useFilter($useFilter){
		if($useFilter)
			$this->renderFilter();
		else {
			/** 
			* @todo clear filter
			*/
			
		}
	}
	
	public function getFilesObj(&$objs)
	{
		if($this->_isCorrect){
			$objs[] = $this;
		}
	}
	
	public function getFilePath(&$paths)
	{
		
		if($this->_isCorrect){
			$paths[] = $this->_path;
		}
	}
	
	public function getDirsName(&$dirs)
	{
		return false;
	}
	
	public function setFilesFilter($filter)
	{
		$this->_filter = $filter;
	}
	
	public function getExtension($fileName = '')
	{
		if($fileName === ''){
			$path_parts = pathinfo($this->_filename);
		} else {
			$path_parts = pathinfo($fileName);
		}
		if(isset($path_parts["extension"]))
			return $path_parts["extension"];
		else
			return '';			
	}
	
	public function getName(){
		return basename($this->_filename,'.'.$this->getExtension());
	}
	
	public function renderFilter()
	{
		if(isset($this->_filter) && count($this->_filter)>0 && $this->filtered==false){
			$this->filtered = true;
			
			if(isset($this->_filter['extension'])){
				$filter = $this->_filter['extension'];
				if(is_array($filter)){
					if(in_array($this->getExtension(),$filter)){
						$this->_isCorrect = true;
					} else {
						$this->_isCorrect = false;
					}
				} else {
					if($this->getExtension()==$filter){
						$this->_isCorrect = true;
					} else {
						$this->_isCorrect = false;
					}
				}
				
			}
			if(isset($this->_filter['name'])){
				$filter = $this->_filter['name'];
				if(is_array($filter)){
					if(in_array($this->getName(),$filter)){
						$this->_isCorrect = true;
					} else {
						$this->_isCorrect = false;
					}
				} else {
					if($this->getName()==$filter){
						$this->_isCorrect = true;
					} else {
						$this->_isCorrect = false;
					}
				}
				
			}
			
		}
	}
	
	public function toArray(&$arr)
	{
		if($this->_isCorrect){
			$arr['files_in_dirs'][] = $this->_filename;
		}
	}
	
	public function toXml(&$xml,$recursionLevel=0,$addOpenTag=true,$rootName='Struct'){
		if($this->_isCorrect){
			$xml .=str_repeat("\t",$recursionLevel+2).'<fileName>'.$this->_filename.'</fileName>'."\n";
		}
	}
	
}

?>