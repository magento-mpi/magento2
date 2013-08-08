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
 * Watermark position config source model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Config_Source_Watermark_Position implements Magento_Core_Model_Option_ArrayInterface
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
