<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config centinel model
 */
namespace Magento\Centinel\Model;

class Config
{
    /**
     * Store id or store model
     *
     * @var int|\Magento\Store\Model\Store
     */
    protected $_store = false;

    /**
     * Path of centinel config
     *
     * @var string
     */
    protected $_serviceConfigPath = 'payment_services/centinel';

    /**
     * Core store config
     *
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * Core config interface
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_coreConfig;

    /**
     * Encryptor interface
     *
     * @var \Magento\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @param \Magento\Store\Model\Config $coreStoreConfig
     * @param \Magento\App\Config\ScopeConfigInterface $coreConfig
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Store\Model\Config $coreStoreConfig,
        \Magento\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Encryption\EncryptorInterface $encryptor
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_coreConfig = $coreConfig;
        $this->_encryptor = $encryptor;
    }

    /**
     * Set store to congif model
     *
     * @param int|\Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Return store
     *
     * @return int|\Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Return centinel processorId
     *
     * @return string
     */
    public function getProcessorId()
    {
        return $this->_getServiceConfigValue('processor_id');
    }

    /**
     * Return centinel merchantId
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->_getServiceConfigValue('merchant_id');
    }

    /**
     * Return centinel transactionPwd
     *
     * @return string
     */
    public function getTransactionPwd()
    {
        return $this->_encryptor->decrypt($this->_getServiceConfigValue('password'));
    }

    /**
     * Return flag - is centinel mode test
     *
     * @return bool
     */
    public function getIsTestMode()
    {
        return (bool)(int)$this->_getServiceConfigValue('test_mode');
    }

    /**
     * Return value of node of centinel config section
     *
     * @param string $key
     * @return string
     */
    private function _getServiceConfigValue($key)
    {
        return $this->_coreStoreConfig->getValue($this->_serviceConfigPath . '/' . $key, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $this->getStore());
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return $this->_getServiceConfigValue('debug');
    }
}
