<?php
/**
 * Helper for errors processing.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Dispatcher_ErrorProcessor
{
    const DEFAULT_SHUTDOWN_FUNCTION = 'apiShutdownFunction';

    const DEFAULT_ERROR_HTTP_CODE = 500;
    const DEFAULT_RESPONSE_CHARSET = 'UTF-8';

    /**#@+
     * Error data representation formats.
     */
    const DATA_FORMAT_JSON = 'json';
    const DATA_FORMAT_XML = 'xml';
    const DATA_FORMAT_URL_ENCODED_QUERY = 'url_encoded_query';
    /**#@-*/

    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;

    /** @var Mage_Core_Helper_Data */
    protected $_helper;

    /** @var Mage_Core_Model_App */
    protected $_app;

    /**
     * Initialize report directory.
     */
    public function __construct(Mage_Core_Model_Factory_Helper $helperFactory, Mage_Core_Model_App $app)
    {
        $this->_helperFactory = $helperFactory;
        $this->_helper = $helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_app = $app;
        $this->registerShutdownFunction();
    }

    /**
     * Save error report.
     *
     * @param string $reportData
     * @return Mage_Webapi_Controller_Dispatcher_ErrorProcessor
     */
    public function saveReport($reportData)
    {
        // TODO refactor method using Varien_Io_File class functions.
        /** Directory for API related reports. */
        /** @see Error_Processor::__construct() */
        $reportDir = BP . DS . 'var' . DS . 'report' . DS . 'api';
        if (!file_exists($reportDir)) {
            @mkdir($reportDir, 0777, true);
        }
        $reportId = abs(intval(microtime(true) * rand(100, 1000)));
        $reportFile = $reportDir . DS . $reportId;
        @file_put_contents($reportFile, serialize($reportData));
        @chmod($reportFile, 0777);
        return $this;
    }

    /**
     * Process API exception.
     *
     * Create report if not in developer mode and render error to send correct API response.
     *
     * @param Exception $exception
     * @param int $httpCode
     */
    public function renderException(Exception $exception, $httpCode = self::DEFAULT_ERROR_HTTP_CODE)
    {
        if ($this->_app->isDeveloperMode() || $exception instanceof Mage_Webapi_Exception) {
            $this->render($exception->getMessage(), $exception->getTraceAsString(), $httpCode);
        } else {
            $this->saveReport($exception->getMessage() . ' : ' . $exception->getTraceAsString());
            $this->render($this->_helper->__('Internal Error'), 'Trace is not available.', $httpCode);
        }
    }

    /**
     * Render error according to mime type.
     *
     * @param string $errorMessage
     * @param string $trace
     * @param int $httpCode
     */
    public function render($errorMessage, $trace = 'Trace is not available.', $httpCode = self::DEFAULT_ERROR_HTTP_CODE)
    {
        if (strstr($_SERVER['HTTP_ACCEPT'], 'json')) {
            $output = $this->_formatError($errorMessage, $trace, $httpCode, self::DATA_FORMAT_JSON);
            $mimeType = 'application/json';
        } elseif (strstr($_SERVER['HTTP_ACCEPT'], 'xml')) {
            $output = $this->_formatError($errorMessage, $trace, $httpCode, self::DATA_FORMAT_XML);
            $mimeType = 'application/xml';
        } elseif (strstr($_SERVER['HTTP_ACCEPT'], 'text/plain')) {
            $output = $this->_formatError($errorMessage, $trace, $httpCode, self::DATA_FORMAT_URL_ENCODED_QUERY);
            $mimeType = 'text/plain';
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
    protected function _formatError(
        $errorMessage,
        $trace,
        $httpCode,
        $format
    ) {
        $errorData = array();
        $message = array('code' => $httpCode, 'message' => $errorMessage);
        if ($this->_app->isDeveloperMode()) {
            $message['trace'] = $trace;
        }
        $errorData['messages']['error'][] = $message;
        switch ($format) {
            case self::DATA_FORMAT_JSON:
                $errorData = $this->_helper->jsonEncode($errorData);
                break;
            case self::DATA_FORMAT_XML:
                $errorData = '<?xml version="1.0"?>'
                    . '<error>'
                    . '<messages>'
                    . '<error>'
                    . '<data_item>'
                    . '<code>' . $httpCode . '</code>'
                    . '<message>' . $errorMessage . '</message>'
                    . ($this->_app->isDeveloperMode() ? '<trace><![CDATA[' . $trace . ']]></trace>' : '')
                    . '</data_item>'
                    . '</error>'
                    . '</messages>'
                    . '</error>';
                break;
            case self::DATA_FORMAT_URL_ENCODED_QUERY:
                $errorData = http_build_query($errorData);
                break;
        }
        return $errorData;
    }

    /**
     * Declare web API-specific shutdown function.
     *
     * @return  Mage_Webapi_Controller_Dispatcher_ErrorProcessor
     */
    public function registerShutdownFunction()
    {
        register_shutdown_function(array($this, self::DEFAULT_SHUTDOWN_FUNCTION));
        return $this;
    }

    /**
     * Function to catch errors, that has not been caught by the user error dispatcher function.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    // TODO: Check if this function is useful (it was migrated from M1)
    public function apiShutdownFunction()
    {
        $fatalErrorFlag = E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR;
        $error = error_get_last();
        if ($error && ($error['type'] & $fatalErrorFlag)) {
            $errorMessage = '';
            switch ($error['type']) {
                case E_ERROR:
                    $errorMessage .= "Fatal Error";
                    break;
                case E_PARSE:
                    $errorMessage .= "Parse Error";
                    break;
                case E_CORE_ERROR:
                    $errorMessage .= "Core Error";
                    break;
                case E_COMPILE_ERROR:
                    $errorMessage .= "Compile Error";
                    break;
                case E_USER_ERROR:
                    $errorMessage .= "User Error";
                    break;
                case E_RECOVERABLE_ERROR:
                    $errorMessage .= "Recoverable Error";
                    break;
                default:
                    $errorMessage .= "Unknown error ({$error['type']})";
                    break;
            }
            $errorMessage .= ": {$error['message']}  in {$error['file']} on line {$error['line']}";
            try {
                // call registered error dispatcher
                trigger_error("'$errorMessage'", E_USER_ERROR);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
            // TODO: Replace Mage::getIsDeveloperMode() to isDeveloperMode() (Mage_Core_Model_App)
            if (!Mage::getIsDeveloperMode()) {
                $this->saveReport($errorMessage);
            }
            $this->render($errorMessage);
        }
    }
}
