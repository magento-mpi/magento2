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
 * Price types mode source
 *
 * @category   Mage
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Config_Source_Product_Options_Price implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'fixed', 'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Fixed')),
            array('value' => 'percent', 'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('Percent'))
        );
    }
}
