<?php
function getCheckSum($dir = './', $recursive=true)
{
    $result = array();
    $iterator = new DirectoryIterator($dir);
    foreach ($iterator as $file) {
        if (in_array($file->getFilename(), array('.', '..', '.svn'))) {
        	continue;
        }
        if (!strpos($file->getPathname(), DS . 'core')
            && !strpos($file->getPathname(), DS . 'js')
            && !strpos($file->getPathname(), DS . 'app')
            && !strpos($file->getPathname(), DS . 'lib')) {
        	continue;
        }
        if ($file->isFile()) {
            $result[$file->getPathname()] = sha1_file($file->getPathname());
        }
	    elseif ($file->isDir() && $recursive) {
	        $res = getCheckSum($file->getPathname());
	        $result = array_merge($result, $res);
	    }
    }
    return $result;
}

require './app/Mage.php';

$version     = Mage::getVersion();
$fileLocal   = "./var/checksum.{$version}.local";
$localResult = false;
$isWritten   = false;
$fileRef     = "./var/checksum.{$version}.ref";
$refResult   = false;
$diff        = false;
$error       = false;

try {
    if (file_exists($fileLocal)) {
        $localResult = unserialize(file_get_contents($fileLocal))  ;
    }
    else {
        $localResult = getCheckSum();
        $isWritten = @file_put_contents($fileLocal, serialize($localResult));
    }
    if (!is_writeable('./var')) {
        $error = 'Please make folder ./var writeable.';
    }
    else {

    }
    if (!file_exists($fileRef)) {
        throw new Exception("Please put reference checksum to {$fileRef}");
    }
    $refResult = unserialize(file_get_contents($fileRef));
    $diff = array_diff_assoc($localResult, $refResult);
}
catch (Exception $e) {
    $error = $e->getMessage();
}

header('Content-Type: text/plain; charset=UTF-8');
printf("Version:            {$version}
Local checksum:     %s
Reference checksum: %s
Diff:               %s\n",
    (false !== $localResult ? 'OK, ' . ($isWritten ? "has been scanned and written to {$fileLocal}" : "has been read from {$fileLocal}. To scan again, remove it.") : 'N/A'),
    (false !== $refResult ? $fileRef : 'N/A'),
    (false !== $diff ? ($diff ? "\n" . implode("\n", array_keys($diff)) : 'all files match reference.' ) : 'N/A')
);
if ($error) {
    print "\n{$error}";
}

exit;
