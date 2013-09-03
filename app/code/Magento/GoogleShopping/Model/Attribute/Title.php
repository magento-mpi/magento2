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
 * Title attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Attribute_Title extends Magento_GoogleShopping_Model_Attribute_Default
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
        $mapValue = $this->getProductAttributeValue($product);
        $name = $this->getGroupAttributeName();
        if (!is_null($name)) {
            $mapValue = $name->getProductAttributeValue($product);
        }

        if (!is_null($mapValue)) {
            $titleText = $mapValue;
        } elseif ($product->getName()) {
            $titleText = $product->getName();
        } else {
            $titleText = 'no title';
        }
        $titleText = Mage::helper('Magento_GoogleShopping_Helper_Data')->cleanAtomAttribute($titleText);
        $entry->setTitle($entry->getService()->newTitle()->setText($titleText));

        return $entry;
    }
}
