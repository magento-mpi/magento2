<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Usa_Model_Shipping_Carrier_Dhl_Source_Shipmenttype implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $fedex = Mage::getSingleton('Magento_Usa_Model_Shipping_Carrier_Dhl');
        $arr = array();
        foreach ($fedex->getCode('shipment_type') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
