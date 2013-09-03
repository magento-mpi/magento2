<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Price attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_Price extends Magento_GoogleShopping_Model_Attribute_Default
{
    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param \Magento\Gdata\Gshopping\Entry $entry
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {
        $product->setWebsiteId(Mage::app()->getStore($product->getStoreId())->getWebsiteId());
        $product->setCustomerGroupId(
            Mage::getStoreConfig(Magento_Customer_Model_Group::XML_PATH_DEFAULT_ID, $product->getStoreId())
        );

        $store = Mage::app()->getStore($product->getStoreId());
        $targetCountry = Mage::getSingleton('Magento_GoogleShopping_Model_Config')->getTargetCountry($product->getStoreId());
        $isSalePriceAllowed = ($targetCountry == 'US');

        // get tax settings
        $taxHelp = Mage::helper('Magento_Tax_Helper_Data');
        $priceDisplayType = $taxHelp->getPriceDisplayType($product->getStoreId());
        $inclTax = ($priceDisplayType == Magento_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX);

        // calculate sale_price attribute value
        $salePriceAttribute = $this->getGroupAttributeSalePrice();
        $salePriceMapValue = null;
        $finalPrice = null;
        if (!is_null($salePriceAttribute)) {
            $salePriceMapValue = $salePriceAttribute->getProductAttributeValue($product);
        }
        if (!is_null($salePriceMapValue) && floatval($salePriceMapValue) > .0001) {
            $finalPrice = $salePriceMapValue;
        } else if ($isSalePriceAllowed) {
            $finalPrice = Mage::helper('Magento_GoogleShopping_Helper_Price')->getCatalogPrice($product, $store, $inclTax);
        }
        if ($product->getTypeId() != Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $finalPrice = $taxHelp->getPrice($product, $finalPrice, $inclTax, null, null, null, $product->getStoreId());
        }

        // calculate price attribute value
        $priceMapValue = $this->getProductAttributeValue($product);
        $price = null;
        if (!is_null($priceMapValue) && floatval($priceMapValue) > .0001) {
            $price = $priceMapValue;
        } else if ($isSalePriceAllowed) {
            $price = Mage::helper('Magento_GoogleShopping_Helper_Price')->getCatalogRegularPrice($product, $store);
        } else {
            $inclTax = ($priceDisplayType != Magento_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX);
            $price = Mage::helper('Magento_GoogleShopping_Helper_Price')->getCatalogPrice($product, $store, $inclTax);
        }
        if ($product->getTypeId() != Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $price = $taxHelp->getPrice($product, $price, $inclTax, null, null, null, $product->getStoreId());
        }

        if ($isSalePriceAllowed) {
            // set sale_price and effective dates for it
            if ($price && ($price - $finalPrice) > .0001) {
                $this->_setAttributePrice($entry, $product, $targetCountry, $price);
                $this->_setAttributePrice($entry, $product, $targetCountry, $finalPrice, 'sale_price');

                $effectiveDate = $this->getGroupAttributeSalePriceEffectiveDate();
                if (!is_null($effectiveDate)) {
                    $effectiveDate->setGroupAttributeSalePriceEffectiveDateFrom(
                            $this->getGroupAttributeSalePriceEffectiveDateFrom()
                        )
                        ->setGroupAttributeSalePriceEffectiveDateTo($this->getGroupAttributeSalePriceEffectiveDateTo())
                        ->convertAttribute($product, $entry);
                }
            } else {
                $this->_setAttributePrice($entry, $product, $targetCountry, $finalPrice);
                $entry->removeContentAttribute('sale_price_effective_date');
                $entry->removeContentAttribute('sale_price');
            }

            // calculate taxes
            $tax = $this->getGroupAttributeTax();
            if (!$inclTax && !is_null($tax)) {
                $tax->convertAttribute($product, $entry);
            }
        } else {
            $this->_setAttributePrice($entry, $product, $targetCountry, $price);
        }

        return $entry;
    }

    /**
     * Custom setter for 'price' attribute
     *
     * @param \Magento\Gdata\Gshopping\Entry $entry
     * @param string $attribute Google Content attribute name
     * @param mixed $value Fload price value
     * @param string $type Google Content attribute type
     * @param string $name Google Content attribute name
     * @return \Magento\Gdata\Gshopping\Entry
     */
    protected function _setAttributePrice($entry, $product, $targetCountry, $value, $name = 'price')
    {
        $store = Mage::app()->getStore($product->getStoreId());
        $price = $store->convertPrice($value);
        return $this->_setAttribute($entry,
            $name,
            self::ATTRIBUTE_TYPE_FLOAT,
            sprintf('%.2f', $store->roundPrice($price)),
            $store->getDefaultCurrencyCode()
        );
    }
}
