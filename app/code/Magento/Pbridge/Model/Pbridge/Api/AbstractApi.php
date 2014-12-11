<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Model\Pbridge\Api;

use Magento\Framework\Logger;

/**
 * Abstract Pbridge API model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AbstractApi extends \Magento\Framework\Object
{
    /**
     * Api response
     *
     * @var array
     */
    protected $_response = [];

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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Logger model
     *
     * @var Logger
     */
    protected $_logger;

    /**
     * Log adapter factory
     *
     * @var \Magento\Framework\Logger\AdapterFactory
     */
    protected $_logAdapterFactory;

    /**
     * Constructor
     *
     * @param Logger $logger
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Logger\AdapterFactory $logAdapterFactory
     * @param array $data
     */
    public function __construct(
        Logger $logger,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Logger\AdapterFactory $logAdapterFactory,
        array $data = []
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_coreData = $coreData;
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_logAdapterFactory = $logAdapterFactory;
        parent::__construct($data);
    }

    /**
     * Make a call to Payment Bridge service with given request parameters
     *
     * @param array $request
     * @return bool
     * @throws \Exception
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _call(array $request)
    {
        $response = null;
        $debugData = ['request' => $request];
        try {
            $http = new \Magento\Framework\HTTP\Adapter\Curl();
            $config = ['timeout' => 60];
            $http->setConfig($config);
            $http->write(
                \Zend_Http_Client::POST,
                $this->getPbridgeEndpoint(),
                '1.1',
                [],
                $this->_prepareRequestParams($request)
            );
            $response = $http->read();
        } catch (\Exception $e) {
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
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

                throw new \Magento\Framework\Model\Exception(__('Unable to communicate with Payment Bridge service.'));
            }
            if (isset($response['status']) && $response['status'] == 'Success') {
                $this->_response = $response;
                return true;
            }
        } else {
            $response = ['status' => 'Fail', 'error' => __('Empty response received from Payment Bridge.')];
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
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _handleError($response)
    {
        if (isset($response['status']) && $response['status'] == 'Fail' && isset($response['error'])) {
            throw new \Magento\Framework\Model\Exception($response['error']);
        }
        throw new \Magento\Framework\Model\Exception(__('There was a payment gateway internal error.'));
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
        $request = ['data' => $this->_pbridgeData->encrypt(json_encode($request))];
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
        $this->_debugFlag = (bool)$this->_scopeConfig->isSetFlag('payment/pbridge/debug', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($this->_debugFlag) {
            $this->_logAdapterFactory->create(['fileName' => 'payment_pbridge.log'])->log($debugData);
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
