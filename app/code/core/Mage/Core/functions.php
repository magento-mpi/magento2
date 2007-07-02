<?php

/**
 * Class autoload
 *
 * @param string $class
 */
function __autoload($class)
{
    $classFile = uc_words($class, DS).'.php';
    include ($classFile);
}

/**
 * Translator function
 *
 * @param string $text the text to translate
 * @param mixed optional parameters to use in sprintf
 */
function __()
{
    return Mage::getSingleton('core/translate')->translate(func_get_args());
}

/**
 * Tiny function to enhance functionality of ucwords
 *
 * Will capitalize first letters and convert separators if needed
 *
 * @param string $str
 * @param string $destSep
 * @param string $srcSep
 * @return string
 */
function uc_words($str, $destSep='_', $srcSep='_')
{
    return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
}

/**
 * Simple sql format date
 *
 * @param string $format
 * @return string
 */
function now($dayOnly=false)
{
    return date($dayOnly ? 'Y-m-d' : 'Y-m-d H:i:s');
}

/**
 * Custom error handler
 *
 * @param integer $errno
 * @param string $errstr
 * @param string $errfile
 * @param integer $errline
 */
function my_error_handler($errno, $errstr, $errfile, $errline){
    $errno = $errno & error_reporting();
    if($errno == 0) return;
    if(!defined('E_STRICT'))            define('E_STRICT', 2048);
    if(!defined('E_RECOVERABLE_ERROR')) define('E_RECOVERABLE_ERROR', 4096);
    echo "<pre>\n<b>";
    switch($errno){
        case E_ERROR:               echo "Error";                  break;
        case E_WARNING:             echo "Warning";                break;
        case E_PARSE:               echo "Parse Error";            break;
        case E_NOTICE:              echo "Notice";                 break;
        case E_CORE_ERROR:          echo "Core Error";             break;
        case E_CORE_WARNING:        echo "Core Warning";           break;
        case E_COMPILE_ERROR:       echo "Compile Error";          break;
        case E_COMPILE_WARNING:     echo "Compile Warning";        break;
        case E_USER_ERROR:          echo "User Error";             break;
        case E_USER_WARNING:        echo "User Warning";           break;
        case E_USER_NOTICE:         echo "User Notice";            break;
        case E_STRICT:              echo "Strict Notice";          break;
        case E_RECOVERABLE_ERROR:   echo "Recoverable Error";      break;
        default:                    echo "Unknown error ($errno)"; break;
    }
    echo ":</b> <i>$errstr</i> in <b>$errfile</b> on line <b>$errline</b><br>";

    $backtrace = debug_backtrace();
    array_shift($backtrace);
    foreach($backtrace as $i=>$l){
        echo "[$i] in <b>"
            .(!empty($l['class']) ? $l['class'] : '')
            .(!empty($l['type']) ? $l['type'] : '')
            ."{$l['function']}</b>(";
        if(!empty($l['args'])) foreach ($l['args'] as $i=>$arg) {
            if ($i>0) echo ", ";
            if (is_object($arg)) echo get_class($arg);
            elseif (is_string($arg)) echo '"'.substr($arg,0,30).'"';
            elseif (is_null($arg)) echo 'NULL';
            elseif (is_numeric($arg)) echo $arg;
            elseif (is_array($arg)) echo "Array[".sizeof($arg)."]";
            else print_r($arg);
        }
        echo ")";
        if(!empty($l['file'])) echo " in <b>{$l['file']}</b>";
        if(!empty($l['line'])) echo " on line <b>{$l['line']}</b>";
        echo "<br>";
    }

    echo "\n</pre>";
    switch ($errno) {
        case E_ERROR:
            die('fatal');
    }
}