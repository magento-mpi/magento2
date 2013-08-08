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
 * Price types mode source
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Config_Source_Product_Options_Price implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'fixed', 'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Fixed')),
            array('value' => 'percent', 'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Percent'))
        );
    }
}
