<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Dhl_Source_Protection_Rounding
    extends Magento_Usa_Model_Shipping_Carrier_Dhl_Source_Generic
{
    public function toOptionArray()
    {
        $carrier = $this->_shippingDhl;
        $arr = array();
        foreach ($carrier->getAdditionalProtectionRoundingTypes() as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
