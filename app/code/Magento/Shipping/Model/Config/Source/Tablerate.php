<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Config\Source;

class Tablerate implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $tableRate = \Mage::getSingleton('Magento\Shipping\Model\Carrier\Tablerate');
        $arr = array();
        foreach ($tableRate->getCode('condition_name') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
