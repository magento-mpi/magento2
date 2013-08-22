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
 * Product view abstract block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Catalog_Block_Product_View_Abstract extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Retrive product
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = parent::getProduct();
        if (is_null($product->getTypeInstance()->getStoreFilter($product))) {
            $product->getTypeInstance()->setStoreFilter(Mage::app()->getStore(), $product);
        }

        return $product;
    }
}
