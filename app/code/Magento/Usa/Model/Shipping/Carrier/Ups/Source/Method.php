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

class Method
{
    public function toOptionArray()
    {
        $ups = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Ups');
        $arr = array();
        foreach ($ups->getCode('method') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>__($v));
        }
        return $arr;
    }
}
