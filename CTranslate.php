<?php
class Translate {
	private static $opts; # object of MultyGetopt
	private static $csv; # object of Varien_File_Csv_multy
	private static $parseData; # parsering data file "__()";
	private static $CONFIG; # data from config.inc.php
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
		$dir = $path.self::$CONFIG['paths']['locale'].$validate.'/';
		$dir_en = $path.self::$CONFIG['paths']['locale'].'en_US/';
		
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
	    	self::callGenerate($file, $path, $dir_en);
		    return;
	    }

	}
	/**
     *Call generation process
     *
     * @param   string $file - files array
     * @param   string $path - root path
     * @param   string $dir_en - dir to default english files
     * @param   int $level - level of recursion
     * @return  none
     */	
	protected static function callGenerate($file,  $path, $dir_en, $level=0){
		static $files_name_changed = array();
		if(!($file===null || $file === false  || $file === true) ){
			if(!is_array($file)){
				if(isset(self::$CONFIG['translates'][$file])){
						self::$parseData = array();
						
							$dirs='';
							$files = '';
							foreach(self::$CONFIG['translates'][$file] as $dir_name){
								$dir = $path.$dir_name;
								if(is_dir($dir)) {
									CFiles::readpath($dir,$dirs,$files);
									for($a=0;$a<count($files);$a++){
										if(in_array(CFiles::getExt($files[$a]),self::$CONFIG['allow_extensions'])){
											self::parseFile($files[$a],self::$parseData,$dir_name);
										}
									}
								} else {
									self::error("Could not found specific module for file ".$file." in ".self::$CONFIG['paths']['mage']);					
								}
							}
							$dup = self::checkDuplicates(self::$parseData);
							if(file_exists($dir_en.$file.'.'.EXTENSION)){
								try{
									$data_en = self::$csv -> getDataPairs($dir_en.$file.'.'.EXTENSION);
								} catch (Exception $e){
									self::error($e->getMessage());
								}
								$parse_data_arr = array();
								foreach (self::$parseData as $key => $val){
									$parse_data_arr[$val['value']]=array('line'=>$val['line'].' - '.$val['file']);
								}
								
								$res = self::checkArray($data_en,$parse_data_arr);
								
								$res['duplicate'] = $dup;
								self::output($file,$res,$file);
								
								$unique_array = array();
								$csv_data = array();
								foreach (self::$parseData as $val){
									array_push($unique_array,$val['value']);
								}
								$unique_array = array_unique($unique_array);
								sort($unique_array);
								foreach ($unique_array as $val){
									if(isset($data_en[$val]['value'])){
										array_push($csv_data,array($val,$data_en[$val]['value']));
									}
									else 
										array_push($csv_data,array($val,$val))	;
								}
								self::$csv -> saveData('output/'.$file.'.'.EXTENSION,$csv_data);
								array_push($files_name_changed,$file);
							}
				} else {
					print "Skip ".$file." (not found configuration for this module in config.inc.php\n";
				}
			} else {
				for($a=0;$a<count($file);$a++){
					self::callGenerate($file[$a],$path,$dir_en,$level+1);					
				}
			
			}
			

		} else {
			foreach (self::$CONFIG['translates'] as $key=>$val){
				self::callGenerate($key,$path,$dir_en,$level+1);	
			}
	    }
		if(isset($files_name_changed) && $level==0){
			print "Created diffs:\n";
			foreach ($files_name_changed as $val){
				print "\toutput/changes/".$val.".".EXTENSION."\n";
			}
			print "Created files:\n";
			foreach ($files_name_changed as $val){
				print "\toutput/".$val.".".EXTENSION."\n";
			}

		}
	}
	
	/**
     *return array of duplicate parsering data
     *
     * @param   array $data - array of data 
     * @return  array - duplicates array
     */
	public static function checkDuplicates($data){
		$dupl = array();
		$check_arr = array();
		foreach ($data as $val){
			if(isset($check_arr[$val['value']])){
				if(isset($dupl[$val['value']])){
					$dupl[$val['value']]['line'].=', '.	$val['line'].'-'.$val['file'];
				} else {
					$dupl[$val['value']]['line']=$check_arr[$val['value']].', '.$val['line'].'-'.$val['file'];
				}
			} else {
				$check_arr[$val['value']] = $val['line'].'-'.$val['file'];
			}
		}
		return $dupl;
	}
	/**
     *Parsering file on "__()"
     *
     * @param   string $file - file path
     * @return  none
     */
	protected static function parseFile($file,&$data_arr,$dir_name){
		try {
			$f = fopen($file,"r");
		} catch (Exception $e) {
			self::error($e->getMessage());
		}
		$line_num = 0;
		while (!feof($f)) {
			$line = fgets($f, 4096);
			$line_num++;
			$results = array();
			preg_match_all('/__\([\s]*([\'|\\\"])(.*?[^\\\\])\\1.*?\)/',$line,$results,PREG_SET_ORDER);
			for($a=0;$a<count($results);$a++){
				$inc_arr = array();
				if(isset($results[$a][2])){
					$inc_arr['value']=$results[$a][2];
					$inc_arr['line']=$line_num;					
					$inc_arr['file']=$file;
					array_push($data_arr,$inc_arr);		
				}
			}
		}
		return $data_arr;
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
		if(!($file===null || $file === false || $file === true ) ){
			if(!is_array($file)){
				self::checkFiles($dir_en.$file.'.'.EXTENSION,$dir.$file.'.'.EXTENSION);
			} else {
				for($i=0;$i<count($file);$i++){
					self::checkFiles($dir_en.$file[$i].'.'.EXTENSION,$dir.$file[$i].'.'.EXTENSION);
				}
			}
	    } else {
	    	$handle = opendir($dir);
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
		//print_r($arr_en);
		foreach ($arr_en as $key=>$val){
			if(!isset($arr[$key])) {
				$err['missing'][$key] = array();
				$err['missing'][$key]['line']=$arr_en[$key]['line'];
				$err['missing'][$key]['value']=$arr_en[$key]['value'];
			}
		}

		foreach ($arr as $key=>$val){
			if(!isset($arr_en[$key])) {
				$err['redundant'][$key] = array();
				$err['redundant'][$key]['line']=$val['line'];
				if(!isset($val['value'])){
					$val['value'] = $key;
				}
				$err['redundant'][$key]['value']=$val['value'];
			}
		}

		foreach ($arr as $key=>$val){
			if(isset($val['duplicate'])){
				$err['duplicate'][$key]['line'] = $val['duplicate']['line'];
				$err['duplicate'][$key]['value'] = $val['duplicate']['value'];
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
		self::output(basename($file),self::checkArray($data_en,$data));
	}
	
	/**
     *Display compared information for pair of files
     *
     * @param   string $file_name - compared file name
     * @param   array $arr - array of lack of coincidences
     * @return  none
     */	
	protected static function output($file_name,$arr,$out_file_name=null){
		$count_miss = count($arr['missing']);
		$count_redu = count($arr['redundant']);
		$count_dupl = count($arr['duplicate']);
		$out ='';
		$out.=$file_name.":\n";
		$tmp_arr = $arr['missing'];
		$arr['missing']=array();
		
		foreach ($tmp_arr as $key=>$val){
			$arr['missing'][$key] = array();
			$arr['missing'][$key]['value'] = $val['value'];
			$arr['missing'][$key]['line'] = $val['line'];
			$arr['missing'][$key]['state'] = 'missing';
		}
		$tmp_arr = $arr['redundant'];
		$arr['redundant']=array();
		
		foreach ($tmp_arr as $key=>$val){
			$arr['redundant'][$key] = array();
			$arr['redundant'][$key]['value'] = $val['value'];
			$arr['redundant'][$key]['line'] = $val['line'];
			$arr['redundant'][$key]['state'] = 'redundant';
		}
		
		
		if($count_redu>0 || $count_dupl>0){
			$comb_arr = array_merge($arr['missing'],$arr['redundant']);
			uksort($comb_arr, 'strcasecmp');
			foreach ($comb_arr as $key=>$val)
			switch ($val['state']){
				case 'missing':
					$out.="\t".'"'.$key.'" => missing'."\n";
					break;
				case 'redundant':
					$out.="\t".'"'.$key.'" => redundant ('.$val['line'].")\n";		
					break;
			}
		
		}

		if($count_dupl>0){
			uksort($arr['duplicate'], 'strcasecmp');
			foreach ($arr['duplicate'] as $key=>$val){
				$out.= "\t".'"'.$key.'" => duplicate ('.$val['line'].")\n";
			}
		}	
		
		if($count_miss>0 || $count_redu>0 || $count_dupl>0){
			if(!$out_file_name){
				echo $out;
			} else {
				$csv_data = array();
				if(isset($comb_arr)){
					foreach ($comb_arr as $key=>$val){
						if(!isset($val['value']))$val['value']=$key;
						switch ($val['state']){
							case 'missing':
								array_push($csv_data,array($key,$val['value'],'missing'));
							break;
							case 'redundant':
								array_push($csv_data,array($key,$val['value'],'redundant ('.$val['line'].')'));	
							break;
						}
					}
				}
				foreach ($arr['duplicate'] as $key=>$val){
					if(!isset($val['value']))$val['value']=$key;
					array_push($csv_data,array($key,$val['value'],'duplicate ('.$val['line'].')'));
				}
				self::$csv -> saveData('output/changes/'.$out_file_name.'.'.EXTENSION,$csv_data);
				}
			}
	}
	
}
?>