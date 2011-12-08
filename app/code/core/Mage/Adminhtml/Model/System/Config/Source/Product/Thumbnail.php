<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog products per page on Grid mode source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Product_Thumbnail
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'itself', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Product Thumbnail Itself')),
            array('value'=>'parent', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Parent Product Thumbnail')),
        );
    }
}
