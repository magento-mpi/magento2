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
 * Virtual product type implementation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Virtual extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
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
}
