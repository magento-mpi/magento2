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
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Retrieve product data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $product = $this->_getProduct();
        /** @var $productHelper Mage_Catalog_Helper_Product */
        $productHelper = Mage::helper('catalog/product');
        $isEnabled = $product->isInStock();
        if (!($isEnabled && $productHelper->canShow($product))) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        $productData = $product->getData();

        // customer group is required in product for correct tier prices calculation
        $product->setCustomerGroupId($this->_getCustomer()->getGroupId());
        $productData['tier_prices'] = $this->_getTierPrices();

        // calculate prices
        $productData['regular_price'] = $product->getPrice();
        $finalPrice = $product->getFinalPrice();
        $productData['final_price'] = $finalPrice;
        $productData['final_price_with_tax'] = $this->_applyTaxToPrice($finalPrice);
        $productData['final_price_without_tax'] = $this->_applyTaxToPrice($finalPrice, false);

        // define URLs
        $productData['image_url'] = $productHelper->getImageUrl($product);
        $productData['url'] = $productHelper->getProductUrl($product->getId());
        /** @var $cartHelper Mage_Checkout_Helper_Cart */
        $cartHelper =Mage::helper('checkout/cart');
        $productData['buy_now_url'] = $cartHelper->getAddUrl($product);

        /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $stockItem->loadByProduct($product);
        $productData['is_in_stock'] = ($stockItem->getId() && $stockItem->getIsInStock());

        /** @var $reviewModel Mage_Review_Model_Review */
        $reviewModel = Mage::getModel('review/review');
        $productData['total_reviews_count'] = $reviewModel->getTotalReviews($product->getId(), true,
            $this->_getStore()->getId());

        $productData['type'] = $product->getTypeId();
        $productData['is_saleable'] = $product->getIsSalable();
        $productData['has_custom_options'] = $product->hasCustomOptions();

        return $productData;
    }

    /**
     * Product update is not available for customer
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Customer does not have permissions for product removal
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Retrive tier prices in special format
     *
     * @return array
     */
    protected function _getTierPrices()
    {
        $tierPrices = array();
        foreach ($this->_getProduct()->getTierPrice() as $tierPrice) {
            $tierPrices[] = array(
                'qty' => $tierPrice['price_qty'],
                'price' => $tierPrice['price'],
                'price_with_tax' => $this->_applyTaxToPrice($tierPrice['price']),
                'price_without_tax' => $this->_applyTaxToPrice($tierPrice['price'], false)
            );
        }
        return $tierPrices;
    }

    /**
     * Define product price with or without taxes
     *
     * @param float $price
     * @param bool $withTax
     * @return float
     */
    protected function _applyTaxToPrice($price, $withTax = true)
    {
        /** @var $taxHelper Mage_Tax_Helper_Data */
        $taxHelper = Mage::helper('tax');
        $customer = $this->_getCustomer();
        return $taxHelper->getPrice($this->_getProduct(), $price, null,
            $customer->getPrimaryShippingAddress(), $customer->getPrimaryBillingAddress(),
            $customer->getTaxClassId(), $this->_getStore(), $withTax);
    }

    /**
     * Retrieve current customer
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        if (is_null($this->_customer)) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer')->load($this->getApiUser()->getUserId());
            if (!$customer->getId()) {
                $this->_critical('Customer not found', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            }
            $this->_customer = $customer;
        }
        return $this->_customer;
    }
}
