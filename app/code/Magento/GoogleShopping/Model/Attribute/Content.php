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
 * Content attribute's model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_Content extends Magento_GoogleShopping_Model_Attribute_Default
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
        $mapValue = $this->getProductAttributeValue($product);
        $description = $this->getGroupAttributeDescription();
        if (!is_null($description)) {
            $mapValue = $description->getProductAttributeValue($product);
        }

        if (!is_null($mapValue)) {
            $descrText = $mapValue;
        } elseif ($product->getDescription()) {
            $descrText = $product->getDescription();
        } else {
            $descrText = 'no description';
        }
        $descrText = Mage::helper('Magento_GoogleShopping_Helper_Data')->cleanAtomAttribute($descrText);
        $entry->setContent($entry->getService()->newContent()->setText($descrText));

        return $entry;
    }
}
