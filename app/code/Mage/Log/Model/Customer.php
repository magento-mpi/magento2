<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer log model
 *
 * @method Mage_Log_Model_Resource_Customer _getResource()
 * @method Mage_Log_Model_Resource_Customer getResource()
 * @method int getVisitorId()
 * @method Mage_Log_Model_Customer setVisitorId(int $value)
 * @method int getCustomerId()
 * @method Mage_Log_Model_Customer setCustomerId(int $value)
 * @method string getLoginAt()
 * @method Mage_Log_Model_Customer setLoginAt(string $value)
 * @method string getLogoutAt()
 * @method Mage_Log_Model_Customer setLogoutAt(string $value)
 * @method int getStoreId()
 * @method Mage_Log_Model_Customer setStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Customer extends Magento_Core_Model_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Log_Model_Resource_Customer');
    }

    /**
     * Load last log by customer id
     *
     * @param Mage_Customer_Model_Customer|int $customer
     * @return Mage_Log_Model_Customer
     */
    public function loadByCustomer($customer)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
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
            return Magento_Date::toTimestamp($loginAt);
        }

        return null;
    }
}
