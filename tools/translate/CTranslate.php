<?php
class Translate {
	private static $opts;
	private static $csv;
	private static $parseData;
	private static $error_array;
	private static $CONFIG;
	/**
     *Starting checking process
     *
     * @param   none
     * @return  none
     */
	public static function run($config){
		self::$CONFIG = $config;
		self::$csv = new Varien_File_Csv_multy();
		
		try {
			self::$opts = new MultyGetopt(array(
			    'path=s'     => 'Path to root directory',
			    'validate-s' => 'Validates selected translation against the default (en_US)',
			    'generate' => 'Generates the default translation (en_US)',
			    'update-s'   => 'Updates the selected translation with the changes (if any) from the default one (en_US)',
			    'dups'     => 'Checks for duplicate keys in different modules (in the default translation en_US)',
			    'file-s'     => 'Make validation of this file(s)'
			  ));
			 self::$opts->setOption('dashDash',false);
			 self::$opts->parse();

		} catch (Zend_Console_Getopt_Exception $e) {
			self::error($e->getUsageMessage());
		}

		$path = self::$opts->getOption('path');
		$validate = self::$opts->getOption('validate');
		$generate = self::$opts->getOption('generate');
		$update = self::$opts->getOption('update');
		$dups = self::$opts->getOption('dups');
		$file = self::$opts->getOption('file');
		$dir = $path.'app/locale/'.$validate.'/';
		$dir_en = $path.'app/locale/en_US/';
		
		if(!is_dir($dir)){
			self::error('Specific dir '.$dir.' is not found');
		}
		if(!is_dir($dir_en)){
			self::error('English dir '.$dir.' is not found');
		}
	    
		if($validate!==null && $validate!==false){
			self::callValidate($file, $dir, $dir_en);
		    return;
	    }
	    if($generate!==null && $generate!==false){
	    	self::callGenerate($file, $path);
		    return;
	    }

	}
	/**
     *Call generation process
     *
     * @param   string $file - files array
     * @param   string $path - root path
     * @return  none
     */	
	protected static function callGenerate($file,  $path){
		
		if(!($file===null || $file === false) ){
			if(!is_array($path.$file)){
				if(is_dir($path.$file)) {
					CFiles::readpath($path.$file,$dirs,$files);
					for($a=0;$a<count($files);$a++){
						if(in_array(CFiles::getExt($files[$a]),self::$CONFIG['allow_extensions'])){
							self::parseFile($files[$a]);
						}
					}
				}
			}
						
			/*if(!is_array($file)){
				self::generateFiles($file,$dir_en);
			} else {
				for($i=0;$i<count($file);$i++){
					self::generateFiles($file[$i],$dir_en);
				}
				}
			*/
	    } else {
	    	self::error("Please specify file(s)");
	    }
	}
	/**
     *Parsering file on "__()"
     *
     * @param   string $file - file path
     * @return  none
     */
	protected static function parseFile($file){
		try {
			$content = file_get_contents($file);
		} catch (Exception $e) {
			self::error($e->getMessage());
		}
		preg_match_all('/__\([\s]*([\'|\\\"])(.*?[^\\\\])\\1.*?\)/',$content,$results,PREG_SET_ORDER);
		print_r($results);
		
	}
	/**
     *Call validation process
     *
     * @param   string $file - files array
     * @param   string $dir - dir to comparing files
     * @param   string $dir_en - dir to default english files
     * @return  none
     */	
	protected static function callValidate($file, $dir, $dir_en){
		if(!($file===null || $file === false) ){
			if(!is_array($file)){
				self::checkFiles($dir_en.$file.'.'.EXTENSION,$dir.$file.'.'.EXTENSION);
			} else {
				for($i=0;$i<count($file);$i++){
					self::checkFiles($dir_en.$file[$i].'.'.EXTENSION,$dir.$file[$i].'.'.EXTENSION);
				}
			}
	    } else {
	    	$handle = opendir($dir);
			$handle_en = opendir($dir_en);
			while (false !== ($file_in_dir = readdir($handle))) {
				if(!is_dir($file_in_dir) && in_array(CFiles::getExt($file_in_dir),self::$CONFIG['allow_extensions'])){
			       	self::checkFiles($dir_en.$file_in_dir,$dir.$file_in_dir);
				}
		    }
	    }
	}
	/**
     *Display error message
     *
     * @param   string $msg - message to display
     * @return  none
     */	
	protected static function error($msg){
		echo "\n".$msg."\n\n";
		exit();
	}
	
	/**
     *Compare arrays with pairs of CSV file's data and return array of lack of coincidences
     *
     * @param   array $arr_en - array of pairs of CSV default english file data
     * @param   array $arr - array of pairs of CSV comparing file data
     * @return  array $array - array of lack of coincidences
     */	
	protected static function checkArray($arr_en,$arr){
		$err = array();
		$err['missing'] = array();
		$err['redundant'] = array();
		$err['duplicate'] = array();

		$duplicates_array = array();
		
		foreach ($arr_en as $key=>$val){
			if(!isset($arr[$key])) {
				$err['missing'][$key] = $arr_en[$key]['line'];
			}
		}

		foreach ($arr as $key=>$val){
			if(!isset($arr_en[$key])) {
				$err['redundant'][$key] = $arr[$key]['line'];
			}
		}

		foreach ($arr as $key=>$val){
			if(isset($arr[$key]['duplicate'])){
				$err['duplicate'][$key] = $arr[$key]['duplicate'];
			}
				
				
			
		}
		return $err;

	}
	
	/**
     *Getting informaton from csv files and calling checking and display fuunctions
     *
     * @param   string $file_en - default english file
     * @param   string $file - comparing file
     * @return  none
     */	
	protected static function checkFiles($file_en,$file){
		try {
			$data_en = self::$csv -> getDataPairs($file_en);
			$data = self::$csv -> getDataPairs($file);
		} catch (Exception $e) {
	 	   self::error($e->getMessage());
		}
		self::displayValidated(basename($file),self::checkArray($data_en,$data));
	}
	
	/**
     *Getting informaton from files and calling generation and display fuunctions
     *
     * @param   string $file_en - default english file
     * @param   string $file - comparing file
     * @return  none
     */	
	protected static function generateFiles($file,$dir_en){
		try {
			$data_en = self::$csv -> getDataPairs($file_en);
			$data = self::$csv -> getDataPairs($file);
		} catch (Exception $e) {
	 	   self::error($e->getMessage());
		}
		self::displayGenerated(basename($file),self::checkArray($data_en,$data));
	}
	
	/**
     *Display compared information for pair of files
     *
     * @param   string $file_name - compared file name
     * @param   array $arr - array of lack of coincidences
     * @return  none
     */	
	protected static function displayValidated($file_name,$arr){
		$count_miss = count($arr['missing']);
		$count_redu = count($arr['redundant']);
		$count_dupl = count($arr['duplicate']);
		
		if($count_miss>0 || $count_redu>0 || $count_dupl>0){
			echo $file_name.":\n";
		}
		
		if($count_miss >0){
			foreach ($arr['missing'] as $key=>$val)
			echo "\t".'"'.$key.'" => missing'."\n";
		}	
		
		if($count_redu>0){
			foreach ($arr['redundant'] as $key=>$val)
			echo "\t".'"'.$key.'" => redundant ('.$val.")\n";
		}

		if($count_dupl>0){
			
			foreach ($arr['duplicate'] as $key=>$val){
				$lines = '';
				foreach ($arr['duplicate'][$key] as $i => $v){
					$lines .= $arr['duplicate'][$key][$i]['line'].', ';
				}
				$lines = rtrim($lines,', ');
				echo "\t".'"'.$key.'" => duplicate ('.$lines.")\n";
			}
		}	
			
	}
	
	/**
     *Display generated information
     *
     * @param   string $file_name - compared file name
     * @param   array $arr - array of lack of coincidences
     * @return  none
     */	
	protected static function displayGenerated($file_name,$arr){
		$count_miss = count($arr['missing']);
		$count_redu = count($arr['redundant']);
		$count_dupl = count($arr['duplicate']);
		
		if($count_miss>0 || $count_redu>0 || $count_dupl>0){
			echo $file_name.":\n";
		}
		
		if($count_miss >0){
			foreach ($arr['missing'] as $key=>$val)
			echo "\t".'"'.$key.'" => missing'."\n";
		}	
		
		if($count_redu>0){
			foreach ($arr['redundant'] as $key=>$val)
			echo "\t".'"'.$key.'" => redundant ('.$val.")\n";
		}

		if($count_dupl>0){
			
			foreach ($arr['duplicate'] as $key=>$val){
				$lines = '';
				foreach ($arr['duplicate'][$key] as $i => $v){
					$lines .= $arr['duplicate'][$key][$i]['line'].', ';
				}
				$lines = rtrim($lines,', ');
				echo "\t".'"'.$key.'" => duplicate ('.$lines.")\n";
			}
		}	
			
	}
	
	

}
?>