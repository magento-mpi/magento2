<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog products per page on Grid mode source
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Config_Source_Product_Thumbnail implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'itself', 'label'=>Mage::helper('Mage_Catalog_Helper_Data')->__('Product Thumbnail Itself')),
            array('value'=>'parent', 'label'=>Mage::helper('Mage_Catalog_Helper_Data')->__('Parent Product Thumbnail')),
        );
    }
}
