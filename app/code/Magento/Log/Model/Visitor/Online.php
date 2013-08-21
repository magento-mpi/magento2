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
 * @method Magento_Log_Model_Resource_Visitor_Online _getResource()
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
        return Mage::app()->loadCache('log_visitor_online_prepare_at');
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
        Mage::app()->saveCache($time, 'log_visitor_online_prepare_at');
        return $this;
    }

    /**
     * Retrieve data update Frequency in second
     *
     * @return int
     */
    public function getUpdateFrequency()
    {
        return Mage::getStoreConfig(self::XML_PATH_UPDATE_FREQUENCY);
    }

    /**
     * Retrieve Online Interval (in minutes)
     *
     * @return int
     */
    public function getOnlineInterval()
    {
        $value = intval(Mage::getStoreConfig(self::XML_PATH_ONLINE_INTERVAL));
        if (!$value) {
            $value = Magento_Log_Model_Visitor::DEFAULT_ONLINE_MINUTES_INTERVAL;
        }
        return $value;
    }
}
