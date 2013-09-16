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
 * Disable magic quotes in runtime if needed
 *
 * @link http://us3.php.net/manual/en/security.magicquotes.disabling.php
 */
if (get_magic_quotes_gpc()) {
    /**
     * Undo magic quotes
     *
     * @param array $array
     * @param bool $topLevel
     * @return array
     */
    function mageUndoMagicQuotes($array, $topLevel = true)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            if (!$topLevel) {
                $newKey = stripslashes($key);
                if ($newKey !== $key) {
                    unset($array[$key]);
                }
                $key = $newKey;
            }
            $newArray[$key] = is_array($value) ? mageUndoMagicQuotes($value, false) : stripslashes($value);
        }
        return $newArray;
    }
    $_GET = mageUndoMagicQuotes($_GET);
    $_POST = mageUndoMagicQuotes($_POST);
    $_COOKIE = mageUndoMagicQuotes($_COOKIE);
    $_REQUEST = mageUndoMagicQuotes($_REQUEST);
}

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
 * Tiny function to enhance functionality of ucwords
 *
 * Will capitalize first letters and convert separators if needed
 *
 * @param string $str
 * @param string $destSep
 * @param string $srcSep
 * @return string
 */
function uc_words($str, $destSep = '_', $srcSep = '_')
{
    return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
}

/**
 * Simple sql format date
 *
 * @param bool $dayOnly
 * @return string
 */
function now($dayOnly = false)
{
    return date($dayOnly ? 'Y-m-d' : 'Y-m-d H:i:s');
}

/**
 * Check whether sql date is empty
 *
 * @param string $date
 * @return boolean
 */
function is_empty_date($date)
{
    return preg_replace('#[ 0:-]#', '', $date) === '';
}

/**
 * Custom error handler
 *
 * @param integer $errorNo
 * @param string $errorStr
 * @param string $errorFile
 * @param integer $errorLine
 * @return bool
 * @throws Exception
 */
function mageCoreErrorHandler($errorNo, $errorStr, $errorFile, $errorLine)
{
    if (strpos($errorStr, 'DateTimeZone::__construct') !== false) {
        // there's no way to distinguish between caught system exceptions and warnings
        return false;
    }

    $errorNo = $errorNo & error_reporting();
    if ($errorNo == 0) {
        return false;
    }
    if (!defined('E_STRICT')) {
        /**
         * Strict error int value
         */
        define('E_STRICT', 2048);
    }
    if (!defined('E_RECOVERABLE_ERROR')) {
        /**
         * Recoverable error int value
         */
        define('E_RECOVERABLE_ERROR', 4096);
    }
    if (!defined('E_DEPRECATED')) {
        /**
         * Deprecated error int value
         */
        define('E_DEPRECATED', 8192);
    }

    // PEAR specific message handling
    if (stripos($errorFile . $errorStr, 'pear') !== false) {
         // ignore strict and deprecated notices
        if (($errorNo == E_STRICT) || ($errorNo == E_DEPRECATED)) {
            return true;
        }
        // ignore attempts to read system files when open_basedir is set
        if ($errorNo == E_WARNING && stripos($errorStr, 'open_basedir') !== false) {
            return true;
        }
    }

    $errorMessage = '';

    switch ($errorNo) {
        case E_ERROR:
            $errorMessage .= "Error";
            break;
        case E_WARNING:
            $errorMessage .= "Warning";
            break;
        case E_PARSE:
            $errorMessage .= "Parse Error";
            break;
        case E_NOTICE:
            $errorMessage .= "Notice";
            break;
        case E_CORE_ERROR:
            $errorMessage .= "Core Error";
            break;
        case E_CORE_WARNING:
            $errorMessage .= "Core Warning";
            break;
        case E_COMPILE_ERROR:
            $errorMessage .= "Compile Error";
            break;
        case E_COMPILE_WARNING:
            $errorMessage .= "Compile Warning";
            break;
        case E_USER_ERROR:
            $errorMessage .= "User Error";
            break;
        case E_USER_WARNING:
            $errorMessage .= "User Warning";
            break;
        case E_USER_NOTICE:
            $errorMessage .= "User Notice";
            break;
        case E_STRICT:
            $errorMessage .= "Strict Notice";
            break;
        case E_RECOVERABLE_ERROR:
            $errorMessage .= "Recoverable Error";
            break;
        case E_DEPRECATED:
            $errorMessage .= "Deprecated functionality";
            break;
        default:
            $errorMessage .= "Unknown error ($errorNo)";
            break;
    }

    $errorMessage .= ": {$errorStr}  in {$errorFile} on line {$errorLine}";
    if (Mage::getIsDeveloperMode()) {
        throw new Exception($errorMessage);
    } else {
        Mage::log($errorMessage, Zend_Log::ERR);
    }
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
 * Create value-object Magento_Phrase
 *
 * @return Magento_Phrase
 */
function __()
{
    $argc = func_get_args();

    return new Magento_Phrase(array_shift($argc), $argc);
}

/**
 * Display exception
 *
 * @param Exception $e
 * @param string $extra
 */
function magePrintException(Exception $e, $extra = '')
{
    if (Mage::getIsDeveloperMode()) {
        print '<pre>';

        if (!empty($extra)) {
            print $extra . "\n\n";
        }

        print $e->getMessage() . "\n\n";
        print $e->getTraceAsString();
        print '</pre>';
    } else {

        $reportData = array(
            !empty($extra) ? $extra . "\n\n" : '' . $e->getMessage(),
            $e->getTraceAsString()
        );

        // retrieve server data
        if (isset($_SERVER)) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $reportData['url'] = $_SERVER['REQUEST_URI'];
            }
            if (isset($_SERVER['SCRIPT_NAME'])) {
                $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
            }
        }
        $objectManager  = Magento_Core_Model_ObjectManager::getInstance();
        // attempt to specify store as a skin
        try {
            $storeCode = $objectManager->get('Magento_Core_Model_StoreManager')->getStore()->getCode();
            $reportData['skin'] = $storeCode;
        } catch (Exception $e) {
        }
        /** @var Magento_Core_Model_Dir $dirs */
        $dirs = $objectManager->get('Magento_Core_Model_Dir');
        require_once($dirs->getDir(Magento_Core_Model_Dir::PUB) . DS . 'errors' . DS . 'report.php');
    }

    die();
}
