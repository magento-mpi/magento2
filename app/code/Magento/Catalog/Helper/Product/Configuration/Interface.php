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
 * Interface for product configuration helpers
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Catalog_Helper_Product_Configuration_Interface
{
    /**
     * Retrieves product options list
     *
     * @param Magento_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getOptions(Magento_Catalog_Model_Product_Configuration_Item_Interface $item);
}
