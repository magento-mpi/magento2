<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Config_Source_Tablerate implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $tableRate = Mage::getSingleton('Magento_Shipping_Model_Carrier_Tablerate');
        $arr = array();
        foreach ($tableRate->getCode('condition_name') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
