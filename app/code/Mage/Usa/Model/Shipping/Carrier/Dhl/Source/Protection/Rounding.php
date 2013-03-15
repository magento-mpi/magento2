<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Usa_Model_Shipping_Carrier_Dhl_Source_Protection_Rounding
{
    public function toOptionArray()
    {
        $carrier = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Dhl');
        $arr = array();
        foreach ($carrier->getAdditionalProtectionRoundingTypes() as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
