<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Usps_Source_Method implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $usps = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Usps');
        $arr = array();
        foreach ($usps->getCode('method') as $v) {
            $arr[] = array('value'=>$v, 'label'=>$v);
        }
        return $arr;
    }
}
