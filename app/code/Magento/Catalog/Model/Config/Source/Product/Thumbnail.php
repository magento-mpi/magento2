<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog products per page on Grid mode source
 *
 * @category   Magento
 * @package    Magento_Catalog
 */
class Magento_Catalog_Model_Config_Source_Product_Thumbnail implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'itself', 'label'=>__('Product Thumbnail Itself')),
            array('value'=>'parent', 'label'=>__('Parent Product Thumbnail')),
        );
    }
}
