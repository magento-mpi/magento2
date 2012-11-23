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
     * Delete data specific for Simple product type
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Simple
     */
    public function deleteTypeSpecificData(Mage_Catalog_Model_Product $product)
    {
        return $this;
    }
}
