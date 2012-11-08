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
 * Simple product type implementation
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Simple extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Check product has weight
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function isWeightDisabled($product)
    {
        return false;
    }
}
