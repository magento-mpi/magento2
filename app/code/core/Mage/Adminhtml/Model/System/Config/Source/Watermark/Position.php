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
 * Watermark position config source model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Watermark_Position
{

    /**
     * Get available options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'stretch',         'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Stretch')),
            array('value' => 'tile',            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Tile')),
            array('value' => 'top-left',        'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Top/Left')),
            array('value' => 'top-right',       'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Top/Right')),
            array('value' => 'bottom-left',     'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Bottom/Left')),
            array('value' => 'bottom-right',    'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Bottom/Right')),
            array('value' => 'center',          'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Center')),
        );
    }

}
