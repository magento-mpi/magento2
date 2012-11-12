<?php
/**
 * Helper for errors processing.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Front_ErrorProcessor
{
    const DEFAULT_ERROR_HTTP_CODE = 500;
    const DEFAULT_ERROR_MESSAGE = 'Resource internal error.';
    const DEFAULT_RESPONSE_CHARSET = 'utf-8';

    /**#@+
     * Error data representation formats.
     */
    const DATA_FORMAT_ARRAY = 'array';
    const DATA_FORMAT_JSON = 'json';
    const DATA_FORMAT_XML = 'xml';
    const DATA_FORMAT_URL_ENCODED_QUERY = 'url_encoded_query';
    /**#@-*/

    /**
     * Directory for API related reports.
     *
     * @var string
     */
    protected $_reportDir;

    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;

    /** @var Mage_Core_Helper_Data */
    protected $_helper;

    /**
     * Initialize report directory.
     */
    public function __construct(Mage_Core_Model_Factory_Helper $helperFactory)
    {
        $this->_helperFactory = $helperFactory;
        $this->_helper = $helperFactory->get('Mage_Webapi_Helper_Data');
        /** @see Error_Processor::__construct() */
        $this->_reportDir = BP . DS . 'var' . DS . 'report' . DS . 'api';
    }

    /**
     * Save error report.
     *
     * @param string $reportData
     * @return Mage_Webapi_Controller_Front_ErrorProcessor
     */
    public function saveReport($reportData)
    {
        // TODO: Is it safe to use '@' here?
        if (!file_exists($this->_reportDir)) {
            @mkdir($this->_reportDir, 0777, true);
        }
        $reportId = abs(intval(microtime(true) * rand(100, 1000)));
        $reportFile = $this->_reportDir . DS . $reportId;
        @file_put_contents($reportFile, serialize($reportData));
        @chmod($reportFile, 0777);
        return $this;
    }

    /**
     * Render error according to mime type.
     *
     * @param string $errorMessage
     * @param string $trace
     * @param int $httpCode
     */
    public function render($errorMessage, $trace, $httpCode = null)
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
    protected function _formatError($errorMessage, $trace, $httpCode = self::DEFAULT_ERROR_HTTP_CODE,
        $format = self::DATA_FORMAT_ARRAY
    ) {
        $errorData = array();
        $message = array('code' => $httpCode, 'message' => $errorMessage);
        if (Mage::getIsDeveloperMode()) {
            $message['trace'] = $trace;
        }
        $errorData['messages']['error'][] = $message;
        switch ($format) {
            case self::DATA_FORMAT_JSON:
                $errorData = $this->_helper->jsonEncode($errorData);
                break;
            case self::DATA_FORMAT_XML:
                $errorData = '<?xml version="1.0"?>'
                    . '<magento_api>'
                    . '<messages>'
                    . '<error>'
                    . '<data_item>'
                    . '<code>' . $httpCode . '</code>'
                    . '<message>' . $errorMessage . '</message>'
                    . (Mage::getIsDeveloperMode() ? '<trace><![CDATA[' . $trace . ']]></trace>' : '')
                    . '</data_item>'
                    . '</error>'
                    . '</messages>'
                    . '</magento_api>';
                break;
            case self::DATA_FORMAT_URL_ENCODED_QUERY:
                $errorData = http_build_query($errorData);
                break;
            case self::DATA_FORMAT_ARRAY:
                break;
        }
        return $errorData;
    }
}
