<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Usa\Model\Shipping\Carrier\Dhl\Source\Protection;

class Value
{
    public function toOptionArray()
    {
        $carrier = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Dhl');
        $arr = array();
        foreach ($carrier->getAdditionalProtectionValueTypes() as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
