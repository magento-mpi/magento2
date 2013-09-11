<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Usa\Model\Shipping\Carrier\Dhl\Source;

class Shipmenttype
{
    public function toOptionArray()
    {
        $fedex = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Dhl');
        $arr = array();
        foreach ($fedex->getCode('shipment_type') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
