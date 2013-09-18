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
 * Virtual product type implementation
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Type_Virtual extends Magento_Catalog_Model_Product_Type_Abstract
{
    /**
     * Check is virtual product
     *
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product)
    {
        return true;
    }

    /**
     * Check that product of this type has weight
     *
     * @return bool
     */
    public function hasWeight()
    {
        return false;
    }

    /**
     * Delete data specific for Virtual product type
     *
     * @param Magento_Catalog_Model_Product $product
     */
    public function deleteTypeSpecificData(Magento_Catalog_Model_Product $product)
    {
    }
}
