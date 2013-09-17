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
 * Content language attribute's model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_ContentLanguage extends Magento_GoogleShopping_Model_Attribute_Default
{
    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Gdata_Gshopping_Entry $entry
     * @return Magento_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {
        $config = Mage::getSingleton('Magento_GoogleShopping_Model_Config');
        $targetCountry = $config->getTargetCountry($product->getStoreId());
        $value = $config->getCountryInfo($targetCountry, 'language', $product->getStoreId());

        return $this->_setAttribute($entry, 'content_language', self::ATTRIBUTE_TYPE_TEXT, $value);
    }
}
