<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wislist model collection
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Resource_Wishlist_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Wishlist_Model_Wishlist', 'Mage_Wishlist_Model_Resource_Wishlist');
    }

    /**
     * Filter collection by customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function filterByCustomer(Mage_Customer_Model_Customer $customer)
    {
        return $this->filterByCustomerId($customer->getId());
    }

    /**
     * Filter collection by customer id
     *
     * @param int $customerId
     * @return Mage_Wishlist_Model_Resource_Wishlist_Collection
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
     * @return Mage_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function filterByCustomerIds(array $customerIds)
    {
        $this->addFieldToFilter('customer_id', array('in' => $customerIds));
        return $this;
    }
}
