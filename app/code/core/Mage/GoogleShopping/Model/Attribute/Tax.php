<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax attribute model
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Attribute_Tax extends Mage_GoogleShopping_Model_Attribute_Default
{
    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Varien_Gdata_Gshopping_Entry $entry
     * @return Varien_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {
        $entry->cleanTaxes();
        if (Mage::helper('Mage_Tax_Helper_Data')->getConfig()->priceIncludesTax()) {
            return $entry;
        }

        $calc = Mage::helper('Mage_Tax_Helper_Data')->getCalculator();
        $customerTaxClass = $calc->getDefaultCustomerTaxClass($product->getStoreId());
        $rates = $calc->getRatesByCustomerAndProductTaxClasses($customerTaxClass, $product->getTaxClassId());
        $targetCountry = Mage::getSingleton('Mage_GoogleShopping_Model_Config')->getTargetCountry($product->getStoreId());
        foreach ($rates as $rate) {
            if ($targetCountry == $rate['country']) {
                $entry->addTax(array(
                    'tax_rate' =>     $rate['value'] * 100,
                    'tax_country' =>  empty($rate['country']) ? '*' : $rate['country'],
                    'tax_region' =>   empty($rate['state']) ? '*' : $rate['state'],
                ));
            }
        }

        return $entry;
    }
}
