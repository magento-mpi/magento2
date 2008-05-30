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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product price block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_Price extends Mage_Core_Block_Template
{
    protected $_priceDisplayType = null;

    public function getProduct()
    {
        return $this->_getData('product');
    }

    public function getDisplayMinimalPrice()
    {
        return $this->_getData('display_minimal_price');
    }

    protected function _calculatePrice($price, $percent, $type)
    {
        $store = Mage::app()->getStore();
        if ($type) {
            return $store->roundPrice($price * (1+($percent/100)));
        } else {
            return $store->roundPrice($price - ($price/(100+$percent)*$percent));
        }
    }

    public function getPrice($product, $price, $includingTax = null)
    {
        $store = Mage::app()->getStore();
        $percent = $product->getTaxPercent();

        if (!$percent) {
            $taxClassId = $product->getTaxClassId();
            if ($taxClassId) {
                $request = Mage::getModel('tax/calculation')->getRateRequest();
                $percent = Mage::getModel('tax/calculation')->getRate($request->setProductClassId($taxClassId));
            }
        }

        if (!$percent) {
            return $price;
        }

        if (is_null($includingTax)) {
            switch (Mage::helper('tax')->needPriceConversion()) {
                case Mage_Tax_Helper_Data::PRICE_CONVERSION_MINUS:
                    return $this->_calculatePrice($price, $percent, false);
                case Mage_Tax_Helper_Data::PRICE_CONVERSION_PLUS:
                    return $this->_calculatePrice($price, $percent, true);
                default:
                    return $price;
            }
        }

        if (Mage::helper('tax')->priceIncludesTax() && !$includingTax) {
            return $this->_calculatePrice($price, $percent, false);
        } else if (!Mage::helper('tax')->priceIncludesTax() && $includingTax) {
            return $this->_calculatePrice($price, $percent, true);
        }
        return $price;
    }

    public function displayPriceIncludingTax()
    {
        return Mage::helper('tax')->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }
    public function displayPriceExcludingTax()
    {
        return Mage::helper('tax')->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }
    public function displayBothPrices()
    {
        return Mage::helper('tax')->getPriceDisplayType() == Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }
}
