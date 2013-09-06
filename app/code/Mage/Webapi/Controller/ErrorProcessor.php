<?php
/**
 * Helper for errors processing.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_ErrorProcessor
{
    const DEFAULT_SHUTDOWN_FUNCTION = 'apiShutdownFunction';

    const DEFAULT_ERROR_HTTP_CODE = 500;
    const DEFAULT_RESPONSE_CHARSET = 'UTF-8';

    /**#@+
     * Error data representation formats.
     */
    const DATA_FORMAT_JSON = 'json';
    const DATA_FORMAT_XML = 'xml';
    /**#@-*/

    /** @var Mage_Core_Helper_Data */
    protected $_coreHelper;

    /** @var Mage_Webapi_Helper_Data */
    protected $_apiHelper;

    /** @var Mage_Core_Model_App */
    protected $_app;

    /** @var Mage_Core_Model_Logger */
    protected $_logger;

    /**
     * Initialize dependencies. Register custom shutdown function.
     *
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Model_Logger $logger
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_App $app,
        Mage_Core_Model_Logger $logger
    ) {
        $this->_coreHelper = $helperFactory->get('Mage_Core_Helper_Data');
        $this->_apiHelper = $helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_app = $app;
        $this->_logger = $logger;
        $this->registerShutdownFunction();
    }

    /**
     * Mask actual exception for security reasons in case when it should not be exposed to API clients.
     *
     * Convert any exception into Mage_Webapi_Exception.
     *
     * @param Exception $exception
     * @return Mage_Webapi_Exception
     */
    public function maskException(Exception $exception)
    {
        if ($exception instanceof Mage_Service_Exception) {
            $maskedException = new Mage_Webapi_Exception(
                $exception->getMessage(),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST,
                $exception->getCode(),
                $exception->getParameters()
            );
        } else if ($exception instanceof Mage_Webapi_Exception) {
            $maskedException = $exception;
        } else {
            if (!$this->_app->isDeveloperMode()) {
                /** Log information about actual exception. */
                $reportId = $this->_logException($exception);
                /** Create exception with masked message. */
                $maskedException = new Mage_Webapi_Exception(
                    $this->_apiHelper
                        ->__('Internal Error. Details are available in Magento log file. Report ID: "%s"', $reportId),
                    Mage_Webapi_Exception::HTTP_INTERNAL_ERROR
                );
            } else {
                $maskedException = new Mage_Webapi_Exception(
                    $exception->getMessage(),
                    Mage_Webapi_Exception::HTTP_INTERNAL_ERROR,
                    $exception->getCode()
                );
            }
        }
        return $maskedException;
    }

    /**
     * Process API exception.
     *
     * Create report if not in developer mode and render error to send correct API response.
     *
     * @param Exception $exception
     * @param int $httpCode
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function renderException(Exception $exception, $httpCode = self::DEFAULT_ERROR_HTTP_CODE)
    {
        if ($this->_app->isDeveloperMode() || $exception instanceof Mage_Webapi_Exception) {
            $this->render($exception->getMessage(), $exception->getTraceAsString(), $httpCode);
        } else {
            $reportId = $this->_logException($exception);
            $this->render(
                $this->_coreHelper
                    ->__('Internal Error. Details are available in Magento log file. Report ID: "%s"', $reportId),
                'Trace is not available.',
                $httpCode
            );
        }
        // TODO: Move die() call to render() method when it will be covered with functional tests.
        die();
    }

    /**
     * Log information about exception to exception log.
     *
     * @param Exception $exception
     * @return string $reportId
     */
    protected function _logException(Exception $exception)
    {
        $exceptionClass = get_class($exception);
        $reportId = uniqid("webapi-");
        $exceptionForLog = new $exceptionClass(
            /** Trace is added separately by logException. */
            "Report ID: $reportId; Message: {$exception->getMessage()}",
            $exception->getCode()
        );
        $this->_logger->logException($exceptionForLog);
        return $reportId;
    }

    /**
     * Render error according to mime type.
     *
     * @param string $errorMessage
     * @param string $trace
     * @param int $httpCode
     */
    public function render(
        $errorMessage,
        $trace = 'Trace is not available.',
        $httpCode = self::DEFAULT_ERROR_HTTP_CODE
    ) {
        if (isset($_SERVER['HTTP_ACCEPT']) && strstr($_SERVER['HTTP_ACCEPT'], 'xml')) {
            $output = $this->_formatError($errorMessage, $trace, $httpCode, self::DATA_FORMAT_XML);
            $mimeType = 'application/xml';
        } else {
            /** Default format is JSON */
            $output = $this->_formatError($errorMessage, $trace, $httpCode, self::DATA_FORMAT_JSON);
            $mimeType = 'application/json';
        }
        if (!headers_sent()) {
            header('HTTP/1.1 ' . ($httpCode ? $httpCode : self::DEFAULT_ERROR_HTTP_CODE));
            header('Content-Type: ' . $mimeType . '; charset=' . self::DEFAULT_RESPONSE_CHARSET);
        }
        echo $output;
    }

    /**
     * Format error data according to required format.
     *
     * @param string $errorMessage
     * @param string $trace
     * @param string $format
     * @param int $httpCode
     * @return array
     */
    protected function _formatError($errorMessage, $trace, $httpCode, $format)
    {
        $errorData = array();
        $message = array('code' => $httpCode, 'message' => $errorMessage);
        if ($this->_app->isDeveloperMode()) {
            $message['trace'] = $trace;
        }
        $errorData['messages']['error'][] = $message;
        switch ($format) {
            case self::DATA_FORMAT_JSON:
                $errorData = $this->_coreHelper->jsonEncode($errorData);
                break;
            case self::DATA_FORMAT_XML:
                $errorData = '<?xml version="1.0"?>'
                    . '<error>'
                    . '<messages>'
                    . '<error>'
                    . '<data_item>'
                    . '<code>' . $httpCode . '</code>'
                    . '<message><![CDATA[' . $errorMessage . ']]></message>'
                    . ($this->_app->isDeveloperMode() ? '<trace><![CDATA[' . $trace . ']]></trace>' : '')
                    . '</data_item>'
                    . '</error>'
                    . '</messages>'
                    . '</error>';
                break;
        }
        return $errorData;
    }

    /**
     * Declare web API-specific shutdown function.
     *
     * @return Mage_Webapi_Controller_ErrorProcessor
     */
    public function registerShutdownFunction()
    {
        register_shutdown_function(array($this, self::DEFAULT_SHUTDOWN_FUNCTION));
        return $this;
    }

    /**
     * Function to catch errors, that has not been caught by the user error dispatcher function.
     */
    public function apiShutdownFunction()
    {
        $fatalErrorFlag = E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR;
        $error = error_get_last();
        if ($error && ($error['type'] & $fatalErrorFlag)) {
            $errorMessage = "Fatal Error: '{$error['message']}' in '{$error['file']}' on line {$error['line']}";
            $reportId = $this->_saveFatalErrorReport($errorMessage);
            if ($this->_app->isDeveloperMode()) {
                $this->render($errorMessage);
            } else {
                $this->render(
                    $this->_apiHelper->__('Server internal error. See details in report api/%s', $reportId)
                );
            }
        }
    }

    /**
     * Log information about fatal error.
     *
     * @param string $reportData
     * @return string
     */
    protected function _saveFatalErrorReport($reportData)
    {
        $file = new Varien_Io_File();
        $reportDir = BP . 'var/report/api';
        $file->checkAndCreateFolder($reportDir, 0777);
        $reportId = abs(intval(microtime(true) * rand(100, 1000)));
        $reportFile = "$reportDir/$reportId";
        $file->write($reportFile, serialize($reportData), 0777);
        return $reportId;
    }
}
