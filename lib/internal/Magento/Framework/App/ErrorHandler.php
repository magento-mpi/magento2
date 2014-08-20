<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App;

/**
 * An error handler that converts runtime errors into exceptions
 */
class ErrorHandler
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $errorPhrases = array(
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Strict Notice',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED => 'Deprecated Functionality',
        E_USER_DEPRECATED => 'User Deprecated Functionality'
    );

    /**
     * Custom error handler
     *
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param int $errorLine
     * @return bool
     * @throws \Exception
     */
    public function handler($errorNo, $errorStr, $errorFile, $errorLine)
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
            if ($errorNo == E_STRICT || $errorNo == E_DEPRECATED) {
                return true;
            }
            // ignore attempts to read system files when open_basedir is set
            if ($errorNo == E_WARNING && stripos($errorStr, 'open_basedir') !== false) {
                return true;
            }
        }
        $errorMessage = isset(
            $this->errorPhrases[$errorNo]
        ) ? $this->errorPhrases[$errorNo] : "Unknown error ({$errorNo})";
        $errorMessage .= ": {$errorStr} in {$errorFile} on line {$errorLine}";
        throw new \Exception($errorMessage);
    }
}
