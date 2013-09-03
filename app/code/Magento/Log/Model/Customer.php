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
 * Customer log model
 *
 * @method Magento_Log_Model_Resource_Customer _getResource()
 * @method Magento_Log_Model_Resource_Customer getResource()
 * @method int getVisitorId()
 * @method Magento_Log_Model_Customer setVisitorId(int $value)
 * @method int getCustomerId()
 * @method Magento_Log_Model_Customer setCustomerId(int $value)
 * @method string getLoginAt()
 * @method Magento_Log_Model_Customer setLoginAt(string $value)
 * @method string getLogoutAt()
 * @method Magento_Log_Model_Customer setLogoutAt(string $value)
 * @method int getStoreId()
 * @method Magento_Log_Model_Customer setStoreId(int $value)
 *
 * @category    Magento
 * @package     Magento_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Log_Model_Customer extends Magento_Core_Model_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Log_Model_Resource_Customer');
    }

    /**
     * Load last log by customer id
     *
     * @param Magento_Customer_Model_Customer|int $customer
     * @return Magento_Log_Model_Customer
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof Magento_Customer_Model_Customer) {
            $customer = $customer->getId();
        }

        return $this->load($customer, 'customer_id');
    }

    /**
     * Return last login at in Unix time format
     *
     * @return int
     */
    public function getLoginAtTimestamp()
    {
        $loginAt = $this->getLoginAt();
        if ($loginAt) {
            return \Magento\Date::toTimestamp($loginAt);
        }

        return null;
    }
}
