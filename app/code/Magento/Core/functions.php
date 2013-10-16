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
 * @throws \Exception
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

    $errorMessage .= ": {$errorStr} in {$errorFile} on line {$errorLine}";
    $exception = new \Exception($errorMessage);
    $errorMessage .= $exception->getTraceAsString();
    $appState = \Magento\Core\Model\ObjectManager::getInstance()->get('Magento\Core\Model\App\State');
    if ($appState == \Magento\Core\Model\App\State::MODE_DEVELOPER) {
        throw $exception;
    } else {
        $dirs = new \Magento\Core\Model\Dir('.');
        $fileSystem = new \Magento\Io\File();
        $logger = new \Magento\Core\Model\Logger($dirs, $fileSystem);
        $logger->log($errorMessage, \Zend_Log::ERR);
    }
}
