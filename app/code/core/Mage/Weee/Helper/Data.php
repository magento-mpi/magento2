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
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * WEEE data helper
 */
class Mage_Weee_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display', $store);
    }

    public function getListPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_list', $store);
    }

    public function getSalesPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_sales', $store);
    }

    public function getEmailPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_email', $store);
    }

    public function getAmount($product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false) {
        return Mage::getModel('weee/tax')->getWeeeAmount($product, $shipping, $billing, $website, $calculateTaxes);
    }

    public function typeOfDisplay($product, $compareTo = null, $zone = null, $store = null)
    {
        $type = 0;
        switch ($zone) {
            case 'product_view':
            $type = $this->getPriceDisplayType($store);
            break;

            case 'product_list':
            $type = $this->getListPriceDisplayType($store);
            break;

            case 'sales':
            $type = $this->getSalesPriceDisplayType($store);
            break;

            case 'email':
            $type = $this->getEmailPriceDisplayType($store);
            break;

            default:
            if (Mage::registry('current_product')) {
                $type = $this->getPriceDisplayType($store);
            } else {
                $type = $this->getListPriceDisplayType($store);
            }
            break;
        }

        if (is_null($compareTo)) {
            return $type;
        } else {
            if (is_array($compareTo)) {
                return in_array($type, $compareTo);
            } else {
                return $type == $compareTo;
            }
        }
    }

    public function getProductWeeeAttributes($product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false)
    {
        return Mage::getModel('weee/tax')->getProductWeeeAttributes($product, $shipping, $billing, $website, $calculateTaxes);
    }

    public function getApplied($item)
    {
        return unserialize($item->getWeeeTaxApplied());
    }

    public function setApplied($item, $value)
    {
        $item->setWeeeTaxApplied(serialize($value));
        return $this;
    }

    public function isDiscounted($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/discount', $store);
    }

    public function isTaxable($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/apply_vat', $store);
    }

    public function includeInSubtotal($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/include_in_subtotal', $store);
    }

    public function getProductWeeeAttributesForDisplay($product)
    {
        return $this->getProductWeeeAttributes($product, null, null, null, $this->typeOfDisplay($product, 1));
    }

    public function getAmountForDisplay($product) {
        return Mage::getModel('weee/tax')->getWeeeAmount($product, null, null, null, $this->typeOfDisplay($product, 1));
    }

    public function getOriginalAmount($product) {
        return Mage::getModel('weee/tax')->getWeeeAmount($product, null, null, null, false, true);
    }

    public function processTierPrices($product, &$tierPrices)
    {
        $weeeAmount = $this->getAmountForDisplay($product);
        foreach ($tierPrices as &$tier) {
            $tier['formated_price_incl_weee'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $tier['website_price'], true)+$weeeAmount));
            $tier['formated_price_incl_weee_only'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $tier['website_price'])+$weeeAmount));
            $tier['formated_weee'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice($weeeAmount));
        }
        return $this;
    }
}
