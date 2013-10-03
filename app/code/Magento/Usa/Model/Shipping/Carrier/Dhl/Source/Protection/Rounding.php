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

class Rounding extends \Magento\Usa\Model\Shipping\Carrier\Dhl\Source\Generic
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
