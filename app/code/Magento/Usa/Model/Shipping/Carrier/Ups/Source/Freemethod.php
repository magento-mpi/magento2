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

class Freemethod extends \Magento\Usa\Model\Shipping\Carrier\Ups\Source\Method
{
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, array('value'=>'', 'label'=>__('None')));
        return $arr;
    }
}
