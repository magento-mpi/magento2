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
     * @var int|\Magento\Core\Model\Store
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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_coreConfig;

    /**
     * @var \Magento\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\App\ConfigInterface $coreConfig
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\App\ConfigInterface $coreConfig,
        \Magento\Encryption\EncryptorInterface $encryptor
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_coreConfig = $coreConfig;
        $this->_encryptor = $encryptor;
    }

    /**
     * Set store to congif model
     *
     * @param int|\Magento\Core\Model\Store $store
     * @return \Magento\Centinel\Model\Config
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Return store
     *
     * @return int|\Magento\Core\Model\Store
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
        return $this->_coreStoreConfig->getConfig($this->_serviceConfigPath . '/' . $key, $this->getStore());
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
