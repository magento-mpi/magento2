<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address service.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Service_Address extends Mage_Core_Service_Abstract
{
    /**
     * Get Customer address by customer_id.
     *
     * @param $customerId
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getByCustomerId($customerId)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Mage_Core_Exception($this->_translateHelper->__("The customer with the specified ID not found."));
        }
        return $customer->getAddresses();
    }
}
