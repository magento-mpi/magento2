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

class Method
{
    public function toOptionArray()
    {
        $usps = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Usps');
        $arr = array();
        foreach ($usps->getCode('method') as $v) {
            $arr[] = array('value'=>$v, 'label'=>$v);
        }
        return $arr;
    }
}
