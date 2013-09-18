<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log Model
 *
 * @method Magento_Log_Model_Resource_Log _getResource()
 * @method Magento_Log_Model_Resource_Log getResource()
 * @method string getSessionId()
 * @method Magento_Log_Model_Log setSessionId(string $value)
 * @method string getFirstVisitAt()
 * @method Magento_Log_Model_Log setFirstVisitAt(string $value)
 * @method string getLastVisitAt()
 * @method Magento_Log_Model_Log setLastVisitAt(string $value)
 * @method int getLastUrlId()
 * @method Magento_Log_Model_Log setLastUrlId(int $value)
 * @method int getStoreId()
 * @method Magento_Log_Model_Log setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Log extends Magento_Core_Model_Abstract
{
    const XML_LOG_CLEAN_DAYS    = 'system/log/clean_after_day';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init Resource Model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Log_Model_Resource_Log');
    }

    public function getLogCleanTime()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_LOG_CLEAN_DAYS) * 60 * 60 * 24;
    }

    /**
     * Clean Logs
     *
     * @return Magento_Log_Model_Log
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
}
