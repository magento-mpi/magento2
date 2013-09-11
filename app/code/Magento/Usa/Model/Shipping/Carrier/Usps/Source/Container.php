<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Usa\Model\Shipping\Carrier\Usps\Source;

class Container
{
    public function toOptionArray()
    {
        $usps = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Usps');
        $arr = array();
        foreach ($usps->getCode('container') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
