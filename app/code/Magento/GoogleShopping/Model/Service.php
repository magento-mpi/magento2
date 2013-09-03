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
class Magento_GoogleShopping_Model_Service extends \Magento\Object
{
    /**
     * Client instance identifier in registry
     *
     * @var string
     */
    protected $_clientRegistryId = 'GCONTENT_HTTP_CLIENT';

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
            if (!Mage::registry($this->_clientRegistryId)) {
                $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass,
                    \Magento\Gdata\Gshopping\Content::AUTH_SERVICE_NAME, null, '', $loginToken, $loginCaptcha,
                    Zend_Gdata_ClientLogin::CLIENTLOGIN_URI, $type
                );
                $configTimeout = array('timeout' => 60);
                $client->setConfig($configTimeout);
                Mage::register($this->_clientRegistryId, $client);
            }
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            throw $e;
        } catch (Zend_Gdata_App_HttpException $e) {
            Mage::throwException($errorMsg . __('Error: %1', $e->getMessage()));
        } catch (Zend_Gdata_App_AuthException $e) {
            Mage::throwException($errorMsg . __('Error: %1', $e->getMessage()));
        }

        return Mage::registry($this->_clientRegistryId);
    }

    /**
     * Set Google Content Client Instance
     *
     * @param Zend_Http_Client $client
     * @return Magento_GoogleShopping_Model_Service
     */
    public function setClient($client)
    {
        Mage::unregister($this->_clientRegistryId);
        Mage::register($this->_clientRegistryId, $client);
        return $this;
    }

    /**
     * Return Google Content Service Instance
     *
     * @param int $storeId
     * @return \Magento\Gdata\Gshopping\Content
     */
    public function getService($storeId = null)
    {
        if (!$this->_service) {
            $this->_service = $this->_connect($storeId);

            if ($this->getConfig()->getIsDebug($storeId)) {
                $this->_service
                    ->setLogAdapter(Mage::getModel('Magento_Core_Model_Log_Adapter',
                    array('fileName' => 'googleshopping.log')), 'log')
                    ->setDebug(true);
            }
        }
        return $this->_service;
    }

    /**
     * Set Google Content Service Instance
     *
     * @param \Magento\Gdata\Gshopping\Content $service
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
        return Mage::getSingleton('Magento_GoogleShopping_Model_Config');
    }

    /**
     * Authorize Google Account
     *
     * @param int $storeId
     * @return \Magento\Gdata\Gshopping\Content service
     */
    protected function _connect($storeId = null)
    {
        $accountId = $this->getConfig()->getAccountId($storeId);
        $client = $this->getClient($storeId);
        $service = new \Magento\Gdata\Gshopping\Content($client, $accountId);
        return $service;
    }
}
