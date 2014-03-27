<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Model\Pbridge\Api;

use Magento\Logger;

/**
 * Abstract Pbridge API model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AbstractApi extends \Magento\Object
{
    /**
     * Api response
     *
     * @var array
     */
    protected $_response = array();

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Pbridge data
     *
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Logger model
     *
     * @var Logger
     */
    protected $_logger;

    /**
     * Log adapter factory
     *
     * @var \Magento\Logger\AdapterFactory
     */
    protected $_logAdapterFactory;

    /**
     * Constructor
     *
     * @param Logger $logger
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Logger\AdapterFactory $logAdapterFactory
     * @param array $data
     */
    public function __construct(
        Logger $logger,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Logger\AdapterFactory $logAdapterFactory,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_coreData = $coreData;
        $this->_logger = $logger;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_logAdapterFactory = $logAdapterFactory;
        parent::__construct($data);
    }

    /**
     * Make a call to Payment Bridge service with given request parameters
     *
     * @param array $request
     * @return bool
     * @throws \Exception
     * @throws \Magento\Model\Exception
     */
    protected function _call(array $request)
    {
        $response = null;
        $debugData = array('request' => $request);
        try {
            $http = new \Magento\HTTP\Adapter\Curl();
            $config = array('timeout' => 60);
            $http->setConfig($config);
            $http->write(
                \Zend_Http_Client::POST,
                $this->getPbridgeEndpoint(),
                '1.1',
                array(),
                $this->_prepareRequestParams($request)
            );
            $response = $http->read();
        } catch (\Exception $e) {
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
            $response = $this->_coreData->jsonDecode(trim($response[1]));

            $debugData['result'] = $response;
            $this->_debug($debugData);

            if ($curlErrorNumber) {
                $this->_logger->logException(
                    new \Exception(
                        sprintf('Payment Bridge CURL connection error #%s: %s', $curlErrorNumber, $curlError)
                    )
                );

                throw new \Magento\Model\Exception(__('Unable to communicate with Payment Bridge service.'));
            }
            if (isset($response['status']) && $response['status'] == 'Success') {
                $this->_response = $response;
                return true;
            }
        } else {
            $response = array('status' => 'Fail', 'error' => __('Empty response received from Payment Bridge.'));
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
     * @throws \Magento\Model\Exception
     */
    protected function _handleError($response)
    {
        if (isset($response['status']) && $response['status'] == 'Fail' && isset($response['error'])) {
            throw new \Magento\Model\Exception($response['error']);
        }
        throw new \Magento\Model\Exception(__('There was a payment gateway internal error.'));
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
        $request = $this->_pbridgeData->getRequestParams($request);
        $request = array('data' => $this->_pbridgeData->encrypt(json_encode($request)));
        return http_build_query($request, '', '&');
    }

    /**
     * Retrieve Payment Bridge servise URL
     *
     * @return string
     */
    public function getPbridgeEndpoint()
    {
        return $this->_pbridgeData->getRequestUrl();
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
            $this->_logAdapterFactory->create(array('fileName' => 'payment_pbridge.log'))->log($debugData);
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
