<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default Error Handler
 *
 * @package Magento\Error
 */
namespace Magento\Error;

class Handler implements HandlerInterface
{
    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dir;

    /**
     * @var bool
     */
    protected $_isDeveloperMode;

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errorPhrases = array(
        E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parse Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Strict Notice',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        E_DEPRECATED        => 'Deprecated Functionality',
        E_USER_DEPRECATED   => 'User Deprecated Functionality'
    );

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Dir $dir
     * @param bool $isDeveloperMode
     * @return \Magento\Error\Handler
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Dir $dir,
        $isDeveloperMode = false
    ) {
        $this->_logger = $logger;
        $this->_dir = $dir;
        $this->_isDeveloperMode = $isDeveloperMode;
    }

    /**
     * Process exception
     *
     * @param \Exception $exception
     * @param string|null $skinCode
     */
    public function processException(\Exception $exception, $skinCode = null)
    {
        if ($this->_isDeveloperMode) {
            print '<pre>';
            print $exception->getMessage() . "\n\n";
            print $exception->getTraceAsString();
            print '</pre>';
        } else {
            $reportData = array($exception->getMessage(), $exception->getTraceAsString(), 'skin' => $skinCode);
            // retrieve server data
            if (isset($_SERVER)) {
                if (isset($_SERVER['REQUEST_URI'])) {
                    $reportData['url'] = $_SERVER['REQUEST_URI'];
                }
                if (isset($_SERVER['SCRIPT_NAME'])) {
                    $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
                }
            }
            require_once($this->_dir->getDir(\Magento\Core\Model\Dir::PUB) . DS . 'errors' . DS . 'report.php');
        }
    }

    /**
     * Show error as exception or log it
     *
     * @throws \Exception
     */
    protected function _processError($errorMessage)
    {
        $exception = new \Exception($errorMessage);
        $errorMessage .= $exception->getTraceAsString();
        if ($this->_isDeveloperMode) {
            throw new \Exception($errorMessage);
        } else {
            $this->_logger->log($errorMessage, \Zend_Log::ERR);
        }
    }

    /**
     * Custom error handler
     *
     * @param int $errorNo
     * @param string $errorStr
     * @param string $errorFile
     * @param int $errorLine
     * @return bool
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
            if (($errorNo == E_STRICT) || ($errorNo == E_DEPRECATED)) {
                return true;
            }
            // ignore attempts to read system files when open_basedir is set
            if ($errorNo == E_WARNING && stripos($errorStr, 'open_basedir') !== false) {
                return true;
            }
        }
        $errorMessage = isset($this->_errorPhrases[$errorNo])
            ? $this->_errorPhrases[$errorNo]
            : "Unknown error ($errorNo)";
        $errorMessage .= ": {$errorStr} in {$errorFile} on line {$errorLine}";
        $this->_processError($errorMessage);
        return true;
    }
}
