<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Shipping_Model_Config_Source_Tablerate implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $tableRate = Mage::getSingleton('Mage_Shipping_Model_Carrier_Tablerate');
        $arr = array();
        foreach ($tableRate->getCode('condition_name') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
