<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Size
{
    public function toOptionArray()
    {
        $usps = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Usps');
        $arr = array();
        foreach ($usps->getCode('size') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
