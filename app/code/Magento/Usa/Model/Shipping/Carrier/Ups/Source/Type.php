<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 * Usa Ups type action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Usa_Model_Shipping_Carrier_Ups_Source_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'UPS', 'label' => Mage::helper('Magento_Usa_Helper_Data')->__('United Parcel Service')),
            #array('value' => Magento_Paypal_Model_Api_Abstract::PAYMENT_TYPE_ORDER, 'label' => Mage::helper('Magento_Usa_Helper_Data')->__('Order')),
            array('value' => 'UPS_XML', 'label' => Mage::helper('Magento_Usa_Helper_Data')->__('United Parcel Service XML')),
        );
    }
}
