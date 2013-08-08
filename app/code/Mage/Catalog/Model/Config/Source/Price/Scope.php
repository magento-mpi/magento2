<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Catalog_Model_Config_Source_Price_Scope implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('Magento_Core_Helper_Data')->__('Global')),
            array('value'=>'1', 'label'=>Mage::helper('Magento_Core_Helper_Data')->__('Website')),
        );
    }
}
