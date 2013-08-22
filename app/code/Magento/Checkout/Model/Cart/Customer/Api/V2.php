<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shoping cart api for customer data 
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Model_Cart_Customer_Api_V2 extends Magento_Checkout_Model_Cart_Customer_Api
{
    /**
     * Prepare customer entered data for implementing
     *
     * @param  object $data
     * @return array
     */
    protected function _prepareCustomerData($data)
    {
        if (null !== ($_data = get_object_vars($data))) {
            return parent::_prepareCustomerData($_data);
        }
        return array();
    }

    /**
     * Prepare customer entered data for implementing
     *
     * @param  object $data
     * @return array
     */
    protected function _prepareCustomerAddressData($data)
    {
        if (is_array($data)) {
            $dataAddresses = array();
            foreach($data as $addressItem) {
                if (null !== ($_addressItem = get_object_vars($addressItem))) {
                    $dataAddresses[] = $_addressItem;
                }
            }
            return parent::_prepareCustomerAddressData($dataAddresses);
        }
        
        return null;
    }
}
