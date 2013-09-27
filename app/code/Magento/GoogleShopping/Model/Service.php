<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Item Types Model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Service extends Magento_Object
{
    /**
     * Client instance identifier in registry
     *
     * @var string
     */
    protected $_clientRegistryId = 'GCONTENT_HTTP_CLIENT';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Config
     *
     * @var Magento_GoogleShopping_Model_Config
     */
    protected $_config;

    /**
     * Log adapter factory
     *
     * @var Magento_Core_Model_Log_AdapterFactory
     */
    protected $_logAdapterFactory;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Core_Model_Log_AdapterFactory $logAdapterFactory
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_GoogleShopping_Model_Config $config
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Log_AdapterFactory $logAdapterFactory,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_GoogleShopping_Model_Config $config,
        array $data = array()
    ) {
        $this->_logAdapterFactory = $logAdapterFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_config = $config;
        parent::__construct($data);
    }

    /**
     * Retutn Google Content Client Instance
     *
     * @param int $storeId
     * @param string $loginToken
     * @param string $loginCaptcha
     * @return Zend_Http_Client
     */
    public function getClient($storeId = null, $loginToken = null, $loginCaptcha = null)
    {
        $user = $this->getConfig()->getAccountLogin($storeId);
        $pass = $this->getConfig()->getAccountPassword($storeId);
        $type = $this->getConfig()->getAccountType($storeId);

        // Create an authenticated HTTP client
        $errorMsg = __('Sorry, but we can\'t connect to Google Content. Please check the account settings in your store configuration.');
        try {
            if (!$this->_coreRegistry->registry($this->_clientRegistryId)) {
                $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass,
                    Magento_Gdata_Gshopping_Content::AUTH_SERVICE_NAME, null, '', $loginToken, $loginCaptcha,
                    Zend_Gdata_ClientLogin::CLIENTLOGIN_URI, $type
                );
                $configTimeout = array('timeout' => 60);
                $client->setConfig($configTimeout);
                $this->_coreRegistry->register($this->_clientRegistryId, $client);
            }
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            throw $e;
        } catch (Zend_Gdata_App_HttpException $e) {
            throw new Magento_Core_Exception($errorMsg . __('Error: %1', $e->getMessage()));
        } catch (Zend_Gdata_App_AuthException $e) {
            throw new Magento_Core_Exception($errorMsg . __('Error: %1', $e->getMessage()));
        }

        return $this->_coreRegistry->registry($this->_clientRegistryId);
    }

    /**
     * Set Google Content Client Instance
     *
     * @param Zend_Http_Client $client
     * @return Magento_GoogleShopping_Model_Service
     */
    public function setClient($client)
    {
        $this->_coreRegistry->unregister($this->_clientRegistryId);
        $this->_coreRegistry->register($this->_clientRegistryId, $client);
        return $this;
    }

    /**
     * Return Google Content Service Instance
     *
     * @param int $storeId
     * @return Magento_Gdata_Gshopping_Content
     */
    public function getService($storeId = null)
    {
        if (!$this->_service) {
            $this->_service = $this->_connect($storeId);

            if ($this->getConfig()->getIsDebug($storeId)) {
                $this->_service->setLogAdapter($this->_logAdapterFactory->create(
                        array('fileName' => 'googleshopping.log')
                    ), 'log')->setDebug(true);
            }
        }
        return $this->_service;
    }

    /**
     * Set Google Content Service Instance
     *
     * @param Magento_Gdata_Gshopping_Content $service
     * @return Magento_GoogleShopping_Model_Service
     */
    public function setService($service)
    {
        $this->_service = $service;
        return $this;
    }

    /**
     * Google Content Config
     *
     * @return Magento_GoogleShopping_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Authorize Google Account
     *
     * @param int $storeId
     * @return Magento_Gdata_Gshopping_Content service
     */
    protected function _connect($storeId = null)
    {
        $accountId = $this->getConfig()->getAccountId($storeId);
        $client = $this->getClient($storeId);
        $service = new Magento_Gdata_Gshopping_Content($client, $accountId);
        return $service;
    }
}
