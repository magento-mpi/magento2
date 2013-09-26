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
 * Prepare Log Online Visitors Model
 *
 * @method Magento_Log_Model_Resource_Visitor_Online getResource()
 * @method string getVisitorType()
 * @method Magento_Log_Model_Visitor_Online setVisitorType(string $value)
 * @method int getRemoteAddr()
 * @method Magento_Log_Model_Visitor_Online setRemoteAddr(int $value)
 * @method string getFirstVisitAt()
 * @method Magento_Log_Model_Visitor_Online setFirstVisitAt(string $value)
 * @method string getLastVisitAt()
 * @method Magento_Log_Model_Visitor_Online setLastVisitAt(string $value)
 * @method int getCustomerId()
 * @method Magento_Log_Model_Visitor_Online setCustomerId(int $value)
 * @method string getLastUrl()
 * @method Magento_Log_Model_Visitor_Online setLastUrl(string $value)
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Visitor_Online extends Magento_Core_Model_Abstract
{
    const XML_PATH_ONLINE_INTERVAL      = 'customer/online_customers/online_minutes_interval';
    const XML_PATH_UPDATE_FREQUENCY     = 'log/visitor/online_update_frequency';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cache;

    /**
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_cache = $cache;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Log_Model_Resource_Visitor_Online');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Magento_Log_Model_Resource_Visitor_Online
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Prepare Online visitors collection
     *
     * @return Magento_Log_Model_Visitor_Online
     */
    public function prepare()
    {
        $this->_getResource()->prepare($this);
        return $this;
    }

    /**
     * Retrieve last prepare at timestamp
     *
     * @return int
     */
    public function getPrepareAt()
    {
        return $this->_cache->load('log_visitor_online_prepare_at');
    }

    /**
     * Set Prepare at timestamp (if time is null, set current timestamp)
     *
     * @param int $time
     * @return Magento_Log_Model_Resource_Visitor_Online
     */
    public function setPrepareAt($time = null)
    {
        if (is_null($time)) {
            $time = time();
        }
        $this->_cache->save($time, 'log_visitor_online_prepare_at');
        return $this;
    }

    /**
     * Retrieve data update Frequency in second
     *
     * @return int
     */
    public function getUpdateFrequency()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_UPDATE_FREQUENCY);
    }

    /**
     * Retrieve Online Interval (in minutes)
     *
     * @return int
     */
    public function getOnlineInterval()
    {
        $value = intval($this->_coreStoreConfig->getConfig(self::XML_PATH_ONLINE_INTERVAL));
        if (!$value) {
            $value = Magento_Log_Model_Visitor::DEFAULT_ONLINE_MINUTES_INTERVAL;
        }
        return $value;
    }
}
