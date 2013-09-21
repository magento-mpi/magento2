<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Dhl_Source_Protection_Value
    extends Magento_Usa_Model_Shipping_Carrier_Dhl_Source_Generic
{
    public function toOptionArray()
    {
        $carrier = $this->_shippingDhl;
        $arr = array();
        foreach ($carrier->getAdditionalProtectionValueTypes() as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
