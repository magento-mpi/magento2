<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for products instance
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Rest_Customer_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * Retrieve product data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $product = $this->_loadProduct();
        /** @var $productHelper Mage_Catalog_Helper_Product */
        $productHelper = Mage::helper('catalog/product');
        $isEnabled = $product->isInStock();
        /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $stockItem->loadByProduct($product);
        $isInStock = ($stockItem->getId() && $stockItem->getIsInStock());

        if (!($isEnabled && $isInStock && $productHelper->canShow($product))) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        $this->_applyTax($product);
        return $product->getData();
    }

    /**
     * Apply taxes to product price. The same behavior as on product page
     *
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _applyTax(Mage_Catalog_Model_Product $product)
    {
        $productPrice = $product->getPrice() ? $product->getPrice() : 0;
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->load($this->getApiUser()->getUserId());
        if (!$customer->getId()) {
            $this->_critical('Customer not found', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }

        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper = Mage::helper('tax');
        $priceAfterTaxProcessing = $taxHelper->getPrice($product, $productPrice, null,
            $customer->getPrimaryShippingAddress(), $customer->getPrimaryBillingAddress(),
            $customer->getTaxClassId(), $this->_getStore());
        $product->setPrice($priceAfterTaxProcessing);
    }

    /**
     * Customer does not have permissions for product removal
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }
}
