<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract Pbridge API model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_Pbridge_Api_Abstract extends Magento_Object
{
    /**
     * Api response
     *
     * @var $_response array
     */
    protected $_response = array();

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Make a call to Payment Bridge service with given request parameters
     *
     * @param array $request
     * @return array
     * @throws Magento_Core_Exception
     */
    protected function _call(array $request)
    {
        $response = null;
        $debugData = array('request' => $request);
        try {
            $http = new Magento_HTTP_Adapter_Curl();
            $config = array('timeout' => 60);
            $http->setConfig($config);
            $http->write(
                Zend_Http_Client::POST,
                $this->getPbridgeEndpoint(),
                '1.1',
                array(),
                $this->_prepareRequestParams($request)
            );
            $response = $http->read();
        } catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $this->_debug($debugData);
            throw $e;
        }

        $this->_debug($response);

        $curlErrorNumber = $http->getErrno();
        $curlError = $http->getError();
        $http->close();

        if ($response) {

            $response = preg_split('/^\r?$/m', $response, 2);
            $response = Mage::helper('Magento_Core_Helper_Data')->jsonDecode(trim($response[1]));

            $debugData['result'] = $response;
            $this->_debug($debugData);

            if ($curlErrorNumber) {
                Mage::logException(new Exception(
                    sprintf('Payment Bridge CURL connection error #%s: %s', $curlErrorNumber, $curlError)
                ));

                Mage::throwException(
                    __('Unable to communicate with Payment Bridge service.')
                );
            }
            if (isset($response['status']) && $response['status'] == 'Success') {
                $this->_response = $response;
                return true;
            }
        } else {
            $response = array(
                'status' => 'Fail',
                'error' => __('Empty response received from Payment Bridge.')
            );
        }

        $this->_handleError($response);
        $this->_response = $response;
        return false;
    }

    /**
     * Handle error of given response
     *
     * @param array $response
     * @return void
     * @throws Magento_Core_Exception
     */
    protected function _handleError($response)
    {
        if (isset($response['status']) && $response['status'] == 'Fail' && isset($response['error'])) {
            Mage::throwException($response['error']);
        }
        Mage::throwException(__('There was a payment gateway internal error.'));
    }

    /**
     * Prepare, merge, encrypt required params for Payment Bridge and payment request params.
     * Return request params as http query string
     *
     * @param array $request
     * @return string
     */
    protected function _prepareRequestParams($request)
    {
        $request = Mage::helper('Magento_Pbridge_Helper_Data')->getRequestParams($request);
        $request = array('data' => Mage::helper('Magento_Pbridge_Helper_Data')->encrypt(json_encode($request)));
        return http_build_query($request, '', '&');
    }

    /**
     * Retrieve Payment Bridge servise URL
     *
     * @return string
     */
    public function getPbridgeEndpoint()
    {
        return Mage::helper('Magento_Pbridge_Helper_Data')->getRequestUrl();
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     * @return void
     */
    protected function _debug($debugData)
    {
        $this->_debugFlag = (bool)$this->_coreStoreConfig->getConfigFlag('payment/pbridge/debug');
        if ($this->_debugFlag) {
            Mage::getModel('Magento_Core_Model_Log_Adapter', array('fileName' => 'payment_pbridge.log'))
               ->log($debugData);
        }
    }

    /**
     * Return API response
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
