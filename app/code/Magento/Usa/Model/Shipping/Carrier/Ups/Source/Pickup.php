<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Usa\Model\Shipping\Carrier\Ups\Source;

class Pickup
{
    public function toOptionArray()
    {
        $ups = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Ups');
        $arr = array();
        foreach ($ups->getCode('pickup') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>__($v['label']));
        }
        return $arr;
    }
}
