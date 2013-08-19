<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fedex freemethod source implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Fedex_Source_Freemethod
    extends Mage_Usa_Model_Shipping_Carrier_Fedex_Source_Method
{
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, array('value' => '', 'label' => __('None')));
        return $arr;
    }
}
