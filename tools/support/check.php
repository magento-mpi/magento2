<?php

function getCheckSum($dir = './', $recursive=true)
{
    $result = array();
    $iterator = new DirectoryIterator($dir);
    foreach ($iterator as $file) {
        if (in_array($file->getFilename(), array('.', '..', '.svn'))) {
        	continue;
        }
        if (!strpos($file->getPathname(), '/core') 
            && !strpos($file->getPathname(), '/js')
            && !strpos($file->getPathname(), '/app')
            && !strpos($file->getPathname(), '/lib')) {
        	continue;
        }
	    if ($file->isDir() && $recursive) {
	        $res = getCheckSum($file->getPathname());
	        $result = array_merge($result, $res);
	    }
	    else {
	    	$result[$file->getPathname()] = sha1_file($file->getPathname());
	    }
    }
    return $result;
}

$result = getCheckSum();

/**
 * Save information about source
 */
//file_put_contents('./check/trunk.res', serialize($result));

/**
 * Compare source with checking code
 */
//$sourceResult = unserialize(file_get_contents('./check/magento-1.1.19700.res'));
//
//$sourceDiff = array_diff_assoc($sourceResult, $result);
//$targetDiff = array_diff_assoc($result, $sourceResult);
//
//echo '<h1>Changed Core Files (count:'.count($sourceDiff).')</h1>';
//foreach ($sourceDiff as $file => $sum) {
//	echo $file.'</br>';
//}