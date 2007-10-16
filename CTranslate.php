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
			    'generate'   => 'Generates the default translation (en_US)',
			    'update-s'   => 'Updates the selected translation with the changes (if any) from the default one (en_US)',
			    'dups'       => 'Checks for duplicate keys in different modules (in the default translation en_US)',
			    'key-s'      => 'Duplication key',
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
		$key_dupl = self::$opts->getOption('key');
		$dir_en = $path.self::$CONFIG['paths']['locale'].'en_US/';
		
		if($validate===null && $dups===null && $update===null && $generate===null){
			self::error('type "php translate.php -h" for help.');
		}
		
		if(!is_dir($dir_en)){
			self::error('Locale dir '.$dir_en.' is not found');
		}
		if($validate===true){
			self::error("Please specify language of validation");
		}
		if($update===true){
			self::error("Please specify language of updating");
		}

		if($validate!==null && $validate!==false){
			$dir = $path.self::$CONFIG['paths']['locale'].$validate.'/';
			self::callValidate($file, $dir, $dir_en);
		    return;
	    }
	    if($generate!==null && $generate!==false){
	    	self::callGenerate($file, $path, $dir_en);
		    return;
	    }
	    if($update!==null && $update!==false){
	       	$dir = $path.self::$CONFIG['paths']['locale'].$update.'/';
	    	self::callUpdate($file, $dir, $dir_en);
		    return;
	    }
	    if($dups!==null && $dups!==false){
	    	if($key_dupl===null || $key_dupl===false || $key_dupl === true) $key_dupl=null;
    		self::callDups($key_dupl,$path);
		    return;
	    }
		

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
		if(!is_dir($dir)){
			self::error('Specific dir '.$dir.' is not found');
		}
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
							$files = array();
							$dirs = array();
							
							CFiles::readpath($dir,$dirs,$files);
							
							for($a=0;$a<count($files);$a++){
								if(in_array(CFiles::getExt($files[$a]),self::$CONFIG['allow_extensions'])){
									self::parseFile($files[$a],self::$parseData);
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
						self::output($file,$res,$dir_en.$file);
						$unique_array = array();
						$csv_data = array();
						foreach (self::$parseData as $val){
							array_push($unique_array,$val['value']);
						}
						$unique_array = array_unique($unique_array);
						natcasesort ($unique_array);
						foreach ($unique_array as $val){
							if(isset($data_en[$val]['value'])){
								array_push($csv_data,array($val,$data_en[$val]['value']));
							}
							else 
								array_push($csv_data,array($val,$val))	;
						}
						
						self::$csv -> saveData($dir_en.$file.'.'.EXTENSION,$csv_data);
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
				print "\t".$val.".".EXTENSION."\n";
			}
			print "Created files:\n";
			foreach ($files_name_changed as $val){
				print "\t".$val.".changes.".EXTENSION."\n";
			}

		}
	}
	/**
     *Call updating process
     *
     * @param   string $file - files array
     * @param   string $dir - dir to comparing files
     * @param   string $dir_en - dir to default english files
     * @return  none
     */	
	protected static function callUpdate($file,  $dir, $dir_en){
		if(!is_dir($dir)){
			self::error('Specific dir '.$dir.' is not found');
		}
		if(!($file===null || $file === false || $file === true ) ){
			if(!is_array($file)){
				$files_name_changed[] = $file;
				self::checkFilesUpdate($dir_en.$file.'.'.EXTENSION,$dir.$file.'.'.EXTENSION);
			} else {
				for($i=0;$i<count($file);$i++){
					$files_name_changed[] = $file[$i];
					self::checkFilesUpdate($dir_en.$file[$i].'.'.EXTENSION,$dir.$file[$i].'.'.EXTENSION);
				}
			}
	    } else {
	    	$handle = opendir($dir);
			while (false !== ($file_in_dir = readdir($handle))) {
				if(!is_dir($file_in_dir) && in_array(CFiles::getExt($file_in_dir),self::$CONFIG['allow_extensions'])){
			       	$files_name_changed[] = $file_in_dir;
			       	self::checkFilesUpdate($dir_en.$file_in_dir,$dir.$file_in_dir);
				}
		    }
	    }
    	if(isset($files_name_changed)){
			print "Created diffs:\n";
			foreach ($files_name_changed as $val){
				print "\t".$val."\n";
			}
			print "Created files:\n";
			foreach ($files_name_changed as $val){
				print "\t".basename($val).".changes.".EXTENSION."\n";
			}

		}
	}
	/**
     *Call duplicat checking process
     *
     * @param   string $key - key checking
     * @param   string $path - path to root
     * @return  none
     */	
	static function callDups($key,$path){
			self::$parseData = array();
			$dirs='';
			$files = '';
			foreach (self::$CONFIG['translates'] as $mod_name=>$path_arr){
				foreach(self::$CONFIG['translates'][$mod_name] as $dir_name){
					$dir = $path.$dir_name;
					if(is_dir($dir)) {
						$files = array();
						$dirs = array();
						CFiles::readpath($dir,$dirs,$files);
						for($a=0;$a<count($files);$a++){
							if(in_array(CFiles::getExt($files[$a]),self::$CONFIG['allow_extensions'])){
								self::parseFile($files[$a],self::$parseData,$mod_name);
							}
						}
					} else {
						self::error("Could not found specific module ".$dir);					
					}
				}
			}
			$dup = self::checkDuplicates(self::$parseData,true);
			if($key===null){
				uksort($dup, 'strcasecmp');
				foreach ($dup as $key=>$val){
					print '"'.$key.'":'."\n";
					$out = $dup[$key]['line'];
					$out = explode(',',$out);
					for($a=0;$a<count($out);$a++){
						print "\t".ltrim($out[$a]," ")."\n";
					}
					print "\n\n";
				}
			} else {
				print '"'.$key.'":'."\n";
				$out = $dup[$key]['line'];
			
				$out = explode(', ',$out);
				for($a=0;$a<count($out);$a++){
					print "\t".ltrim($out[$a]," ")."\n";
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
			if(isset($val['mod_name'])){
				$mod_name = $val['mod_name'].' from ';
			} else {
				$mod_name = '';
			}
			if(isset($check_arr[$val['value']])){
				if(isset($dupl[$val['value']])){
					$dupl[$val['value']]['line'].=', '.$mod_name.$val['line'].'-'.$val['file'];
				} else {
					$dupl[$val['value']]['line']=$check_arr[$val['value']].', '.$mod_name.$val['line'].'-'.$val['file'];
				}
			} else {
				$check_arr[$val['value']] = $mod_name.$val['line'].'-'.$val['file'];
			}
		}
		return $dupl;
	}
	/**
     *Parsering file on "__()"
     *
     * @param   string $file - file path
     * @param   array &$data_arr - array of data
     * @param   string $mod_name - module name of parsered file
     * @return  array $data_arr
     */
	protected static function parseFile($file,&$data_arr,$mod_name=null){
		$f = fopen($file,"r");
		if(!$f){
			self::error('file '.$file.' not found');
		}
		$line_num = 0;
		if(CFiles::getExt($file)==='xml'){
			$xml = new Varien_Simplexml_Config();
			$xml->loadFile($file,'SimpleXMLElement');
			$arr = $xml->getXpath("//*[@translate]");
			unset($xml);
			if(is_array($arr)){
				foreach ($arr as $val){
					if(is_a($val,"Varien_Simplexml_Element")){	
						if(is_a($val,"Varien_Simplexml_Element")){
							$attr = $val->attributes();
							$transl = $attr['translate'];
							$transl = explode(' ', (string)$transl);
			                foreach ($transl as $v) {
				                $inc_arr['value']=(string)$val->$v;
								$inc_arr['line']='';
								$inc_arr['file']=$file;
								if($mod_name!==null){
									$inc_arr['mod_name'] = $mod_name;
								} else {
									$inc_arr['mod_name'] = ''; 
								}
								array_push($data_arr,$inc_arr);
			                }
						}
					}
				}
			}
		} else {
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
						if($mod_name!==null)$inc_arr['mod_name'] = $mod_name;
						array_push($data_arr,$inc_arr);
					}
				}
			}
		}
		return $data_arr;
	}
	
	/**
     *Display error message
     *
     * @param   string $msg - message to display
     * @return  none
     */
	protected static function error($msg){
        echo "\n" . USAGE . "\n\n";
		echo "ERROR:\n\n".$msg."\n\n";
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
     *Getting informaton from csv files for update
     *
     * @param   string $file_en - default english file
     * @param   string $file - comparing file
     * @return  none
     */	
	protected static function checkFilesUpdate($file_en,$file){
		try {
			$data_en = self::$csv -> getDataPairs($file_en);
			$data = self::$csv -> getDataPairs($file);
		} catch (Exception $e) {
	 	   self::error($e->getMessage());
		}
		$diff_arr = self::checkArray($data_en,$data);
		$path_inf = pathinfo($file);
		
		self::output(basename($file),$diff_arr,$path_inf['dirname']."/".basename($file,".".EXTENSION));
		$pre_data = array();
		$csv_data = array();
		foreach ($data_en as $key=>$val){
			$pre_data[$key]=$val['value'];
		}
		uksort($pre_data, 'strcasecmp');
		foreach ($pre_data as $key => $val){
			if(isset($data[$key]['value'])){
				array_push($csv_data,array($key,$data[$key]['value']));
			} else {
				array_push($csv_data,array($key,$val))	;
			}
		}
		$path_inf = pathinfo($file);
		self::$csv -> saveData($file,$csv_data);
	}
	/**
     *Display compared information for pair of files
     *
     * @param   string $file_name - compared file name
     * @param   array $arr - array of lack of coincidences
     * @return  none
     */
	protected static function output($file_name,$arr,$out_file_name=null){
		
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
		$count_miss = count($arr['missing']);
		$count_redu = count($arr['redundant']);
		$count_dupl = count($arr['duplicate']);
		
		if($count_redu>0 || $count_miss>0){
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
				self::$csv -> saveData($out_file_name.'.changes.'.EXTENSION,$csv_data);
				}
			}
	}

}
?>