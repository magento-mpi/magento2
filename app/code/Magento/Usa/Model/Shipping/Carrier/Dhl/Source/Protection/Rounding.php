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

class Rounding
{
    public function toOptionArray()
    {
        $carrier = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Dhl');
        $arr = array();
        foreach ($carrier->getAdditionalProtectionRoundingTypes() as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
