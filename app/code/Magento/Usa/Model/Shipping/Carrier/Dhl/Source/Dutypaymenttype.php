<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Dhl_Source_Dutypaymenttype
    implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $dhl = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Dhl');
        $arr = array();
        foreach ($dhl->getCode('dutypayment_type') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
