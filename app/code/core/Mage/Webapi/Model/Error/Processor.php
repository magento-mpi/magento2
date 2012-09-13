<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webapi Error Processor
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
// TODO: Refactor this class (it came from Magento 1)
class Mage_Webapi_Model_Error_Processor
{
    /**
     * Default http response code
     */
    const HTTP_ERROR_CODE = 500;

    /**
     * Default response charset
     */
    const RESPONSE_CHARSET = 'utf-8';

    /**
     * Default error message to send
     */
    const ERROR_MESSAGE = 'Resource internal error.';

    /**
     * Report dir for api reports
     *
     * @var string
     */
    protected $_reportDir;

    /**
     * Initialize report directory
     *
     * @see Error_Processor::__construct()
     */
    public function __construct()
    {
        $varDir = 'var';
        $reportDir = 'report';
        $apiReportDir = 'api';
        $this->_reportDir = BP . DS . $varDir . DS . $reportDir . DS . $apiReportDir;
    }

    /**
     * Save error report
     *
     * @param string $reportData
     * @return Mage_Webapi_Model_Error_Processor
     */
    public function saveReport($reportData)
    {
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
     * Render error according to mime type
     *
     * @param string $errorDetailedMessage
     * @param int $httpCode
     */
    public function render($errorDetailedMessage, $httpCode = null)
    {
        if (strstr($_SERVER['HTTP_ACCEPT'], 'json')) {
            $output = $this->_getRenderedJson($errorDetailedMessage);
            $mimeType = 'application/json';
        } elseif (strstr($_SERVER['HTTP_ACCEPT'], 'xml')) {
            $output = $this->_getRenderedXml($errorDetailedMessage);
            $mimeType = 'application/xml';
        } elseif (strstr($_SERVER['HTTP_ACCEPT'], 'text/plain')) {
            $output = $this->_getRenderedQuery($errorDetailedMessage);
            $mimeType = 'text/plain';
        } else {
            $output = $this->_getRenderedJson($errorDetailedMessage);
            $mimeType = 'application/json';
        }

        if (!headers_sent()) {
            header('HTTP/1.1 ' . ($httpCode ? $httpCode : self::HTTP_ERROR_CODE));
            header('Content-Type: ' . $mimeType . '; charset=' . self::RESPONSE_CHARSET);
        }

        echo $output;
    }

    /**
     * Get output data as xml string
     *
     * @param string $errorDetailedMessage
     * @return string
     */
    protected function _getRenderedXml($errorDetailedMessage)
    {
        $output = '<?xml version="1.0"?>'
            . '<magento_api>'
            . '<messages>'
            . '<error>'
            . '<data_item>'
            . '<code>' . self::HTTP_ERROR_CODE . '</code>'
            . '<message>' . self::ERROR_MESSAGE . '</message>'
            . (Mage::getIsDeveloperMode() ? '<trace><![CDATA[' . $errorDetailedMessage . ']]></trace>' : '')
            . '</data_item>'
            . '</error>'
            . '</messages>'
            . '</magento_api>';

        return $output;
    }

    /**
     * Get output data as json
     *
     * @param string $errorDetailedMessage
     * @return string
     */
    protected function _getRenderedJson($errorDetailedMessage)
    {
        return Zend_Json::encode($this->_getErrorData($errorDetailedMessage));
    }

    /**
     * Get output data as URL-encoded query string
     *
     * @param string $errorDetailedMessage
     * @return string
     */
    protected function _getRenderedQuery($errorDetailedMessage)
    {
        return http_build_query($this->_getErrorData($errorDetailedMessage));
    }

    /**
     * Get formatted array with error data
     *
     * @param string $errorDetailedMessage
     * @return array
     */
    protected function _getErrorData($errorDetailedMessage)
    {
        $data = array();
        $message = array('code' => self::HTTP_ERROR_CODE, 'message' => self::ERROR_MESSAGE);
        if (Mage::getIsDeveloperMode()) {
            $message['trace'] = $errorDetailedMessage;
        }
        $data['messages']['error'][] = $message;

        return $data;
    }
}
