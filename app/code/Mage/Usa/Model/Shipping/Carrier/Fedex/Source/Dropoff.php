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
 * Fedex dropoff source implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Fedex_Source_Dropoff
{
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Fedex');
        $arr = array();
        foreach ($fedex->getCode('dropoff') as $k => $v) {
            $arr[] = array('value' => $k, 'label' => $v);
        }
        return $arr;
    }
}
