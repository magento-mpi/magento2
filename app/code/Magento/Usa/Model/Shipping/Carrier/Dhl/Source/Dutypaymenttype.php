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

class Dutypaymenttype
{
    public function toOptionArray()
    {
        $dhl = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Dhl');
        $arr = array();
        foreach ($dhl->getCode('dutypayment_type') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
