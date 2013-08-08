<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product alert for back in stock collection
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Resource_Stock_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define stock collection
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_ProductAlert_Model_Stock', 'Mage_ProductAlert_Model_Resource_Stock');
    }

    /**
     * Add customer filter
     *
     * @param mixed $customer
     * @return Mage_ProductAlert_Model_Resource_Stock_Collection
     */
    public function addCustomerFilter($customer)
    {
        $adapter = $this->getConnection();
        if (is_array($customer)) {
            $condition = $adapter->quoteInto('customer_id IN(?)', $customer);
        } elseif ($customer instanceof Mage_Customer_Model_Customer) {
            $condition = $adapter->quoteInto('customer_id=?', $customer->getId());
        } else {
            $condition = $adapter->quoteInto('customer_id=?', $customer);
        }
        $this->addFilter('customer_id', $condition, 'string');
        return $this;
    }

    /**
     * Add website filter
     *
     * @param mixed $website
     * @return Mage_ProductAlert_Model_Resource_Stock_Collection
     */
    public function addWebsiteFilter($website)
    {
        $adapter = $this->getConnection();
        if (is_null($website) || $website == 0) {
            return $this;
        }
        if (is_array($website)) {
            $condition = $adapter->quoteInto('website_id IN(?)', $website);
        } elseif ($website instanceof Magento_Core_Model_Website) {
            $condition = $adapter->quoteInto('website_id=?', $website->getId());
        } else {
            $condition = $adapter->quoteInto('website_id=?', $website);
        }
        $this->addFilter('website_id', $condition, 'string');
        return $this;
    }

    /**
     * Add status filter
     *
     * @param int $status
     * @return Mage_ProductAlert_Model_Resource_Stock_Collection
     */
    public function addStatusFilter($status)
    {
        $condition = $this->getConnection()->quoteInto('status=?', $status);
        $this->addFilter('status', $condition, 'string');
        return $this;
    }

    /**
     * Set order by customer
     *
     * @param string $sort
     * @return Mage_ProductAlert_Model_Resource_Stock_Collection
     */
    public function setCustomerOrder($sort = 'ASC')
    {
        $this->getSelect()->order('customer_id ' . $sort);
        return $this;
    }
}
