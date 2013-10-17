<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Object destructor
 *
 * @param mixed $object
 */
function destruct($object)
{
    if (is_array($object)) {
        foreach ($object as $obj) {
            destruct($obj);
        }
    }
    unset($object);
}

/**
 * Pretty debug backtrace
 *
 * @param bool $return
 * @param bool $html
 * @param bool $showFirst
 * @return string
 */
function mageDebugBacktrace($return = false, $html = true, $showFirst = false)
{
    $backTrace = debug_backtrace();
    $out = '';
    if ($html) {
        $out .= "<pre>";
    }

    foreach ($backTrace as $index => $trace) {
        if (!$showFirst && $index == 0) {
            continue;
        }
        // sometimes there is undefined index 'file'
        @$out .= "[$index] {$trace['file']}:{$trace['line']}\n";
    }

    if ($html) {
        $out .= "</pre>";
    }

    if ($return) {
        return $out;
    } else {
        echo $out;
    }
}

/**
 * Delete folder recursively
 *
 * @param string $path
 */
function mageDelTree($path)
{
    if (is_dir($path)) {
        $entries = scandir($path);
        foreach ($entries as $entry) {
            if ($entry != '.' && $entry != '..') {
                mageDelTree($path . DS . $entry);
            }
        }
        @rmdir($path);
    } else {
        @unlink($path);
    }
}

/**
 * Parse csv file
 *
 * @param string $string
 * @param string $delimiter
 * @param string $enclosure
 * @return array
 */
function mageParseCsv($string, $delimiter = ",", $enclosure = '"')
{
    $elements = explode($delimiter, $string);
    for ($i = 0; $i < count($elements); $i++) {
        $nQuotes = substr_count($elements[$i], $enclosure);
        if ($nQuotes %2 == 1) {
            for ($j = $i+1; $j < count($elements); $j++) {
                if (substr_count($elements[$j], $enclosure) > 0) {
                    // Put the quoted string's pieces back together again
                    array_splice($elements, $i, $j - $i + 1,
                        implode($delimiter, array_slice($elements, $i, $j - $i + 1)));
                    break;
                }
            }
        }
        if ($nQuotes > 0) {
            // Remove first and last quotes, then merge pairs of quotes
            $qStr =& $elements[$i];
            $qStr = substr_replace($qStr, '', strpos($qStr, $enclosure), 1);
            $qStr = substr_replace($qStr, '', strrpos($qStr, $enclosure), 1);
            $qStr = str_replace($enclosure.$enclosure, $enclosure, $qStr);
        }
    }
    return $elements;
}

/**
 * Check is directory writable or not
 *
 * @param string $dir
 * @return bool
 */
function is_dir_writeable($dir)
{
    if (is_dir($dir) && is_writable($dir)) {
        if (stripos(PHP_OS, 'win') === 0) {
            $dir    = ltrim($dir, DIRECTORY_SEPARATOR);
            $file   = $dir . DIRECTORY_SEPARATOR . uniqid(mt_rand()) . '.tmp';
            $exist  = file_exists($file);
            $fileResource = @fopen($file, 'a');
            if ($fileResource === false) {
                return false;
            }
            fclose($fileResource);
            if (!$exist) {
                unlink($file);
            }
        }
        return true;
    }
    return false;
}

/**
 * Create value-object \Magento\Phrase
 *
 * @return string
 */
function __()
{
    $argc = func_get_args();

    /**
     * Type casting to string is a workaround.
     * Many places in client code at the moment are unable to handle the \Magento\Phrase object properly.
     * The intended behavior is to use __toString(),
     * so that rendering of the phrase happens only at the last moment when needed
     */
    return (string)new \Magento\Phrase(array_shift($argc), $argc);
}
