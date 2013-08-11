<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogInventory_Model_Source_Backorders
{
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_CatalogInventory_Model_Stock::BACKORDERS_NO, 'label'=>Mage::helper('Magento_CatalogInventory_Helper_Data')->__('No Backorders')),
            array('value' => Magento_CatalogInventory_Model_Stock::BACKORDERS_YES_NONOTIFY, 'label'=>Mage::helper('Magento_CatalogInventory_Helper_Data')->__('Allow Qty Below 0')),
            array('value' => Magento_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY , 'label'=>Mage::helper('Magento_CatalogInventory_Helper_Data')->__('Allow Qty Below 0 and Notify Customer')),
        );
    }
}
