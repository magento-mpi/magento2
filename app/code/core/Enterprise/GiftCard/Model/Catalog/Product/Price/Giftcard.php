<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_GiftCard_Model_Catalog_Product_Price_Giftcard extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Return price of the specified product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getPrice($product)
    {
        if ($product->getData('price')) {
            return $product->getData('price');
        } else {
            return 0;
        }
    }

    /**
     * Retrieve product final price
     *
     * @param integer $qty
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getFinalPrice($qty=null, $product)
    {
        $finalPrice = $product->getPrice();
        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('giftcard_amount');
            if ($customOption) {
                $finalPrice += $customOption->getValue();
            }
        }
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);

        $product->setData('final_price', $finalPrice);
        return max(0, $product->getData('final_price'));
    }

    /**
     * Load and set gift card amounts into product object
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function getAmounts($product)
    {
        $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $product->getData('giftcard_amounts');

        if (is_null($prices)) {
            if ($attribute = $product->getResource()->getAttribute('giftcard_amounts')) {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData('giftcard_amounts');
            }
        }

        return ($prices) ? $prices : array();
    }
}
