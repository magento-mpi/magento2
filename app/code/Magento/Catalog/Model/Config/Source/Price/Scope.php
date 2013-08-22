<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Catalog_Model_Config_Source_Price_Scope implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>__('Global')),
            array('value'=>'1', 'label'=>__('Website')),
        );
    }
}
