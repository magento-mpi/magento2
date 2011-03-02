<?php

function getTreeFolders($sRootPath, $iDepth = 0) {
      $iDepth++;
      $aDirs = array();
      $oDir = dir($sRootPath);
      while(($sDir = $oDir->read()) !== false) {
        if($sDir != '.' && $sDir != '..' && $sDir != '.svn') {
            if(is_dir($sRootPath.$sDir.'/') && !$ret=getTreeFolders($sRootPath.$sDir.'/',$iDepth)){
                continue;
            }else{
                //echo(' not empty '.$sRootPath.$sDir . ' ret='.$ret);//exit();
                return true;
            }
        }
      }
      $oDir->close();
      return false;
}


 $fdiff=file('diff.log');
 foreach ($fdiff as $key=>$fitem){
    $fitem=trim($fitem);
    if(preg_match('/Only in ([^:]*): (.*)/', $fitem,$matches)){
        //var_dump($matches);exit();
        $iname=$matches[1].DIRECTORY_SEPARATOR.$matches[2];
        if(is_file($iname)){
            //continue;
        }else{
            if(is_dir($iname)&&getTreeFolders($iname.DIRECTORY_SEPARATOR)){
                $fitem.=' - not empty ';
            }else{
                $fitem.=' - empty ';continue;
            }
        }
    }
    echo("$fitem \n");
 }

?>
