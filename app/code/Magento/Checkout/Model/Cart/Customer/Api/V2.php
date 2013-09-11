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
namespace Magento\Checkout\Model\Cart\Customer\Api;

class V2 extends \Magento\Checkout\Model\Cart\Customer\Api
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
