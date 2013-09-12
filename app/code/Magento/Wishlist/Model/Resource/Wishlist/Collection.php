<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wislist model collection
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Model_Resource_Wishlist_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Wishlist_Model_Wishlist', 'Magento_Wishlist_Model_Resource_Wishlist');
    }

    /**
     * Filter collection by customer
     *
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function filterByCustomer(Magento_Customer_Model_Customer $customer)
    {
        return $this->filterByCustomerId($customer->getId());
    }

    /**
     * Filter collection by customer id
     *
     * @param int $customerId
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function filterByCustomerId($customerId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        return $this;
    }

    /**
     * Filter collection by customer ids
     *
     * @param array $customerIds
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function filterByCustomerIds(array $customerIds)
    {
        $this->addFieldToFilter('customer_id', array('in' => $customerIds));
        return $this;
    }
}
