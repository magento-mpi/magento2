<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Select attributes suitable for product variations generation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Configurable_AttributeSelector extends Mage_Backend_Block_Abstract
{
    /**
     * Retrieve list of attributes with admin store label containing $labelPart
     *
     * @param string $labelPart
     * @return array
     */
    public function getSuggestedAttributes($labelPart)
    {
        $escapedLabelPart = Mage::getResourceHelper('Mage_Core')->addLikeEscape($labelPart, array('position' => 'any'));
        /** @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Attribute_Collection')
            ->addFieldToFilter('frontend_input', 'select')
            ->addFieldToFilter('frontend_label', array('like' => $escapedLabelPart))
            ->addFieldToFilter('is_configurable', 1)
            ->addFieldToFilter('is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);

        $result = array();
        foreach ($collection->getItems() as $id => $attribute) {
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $result[$id] = array(
                'label' => $attribute->getFrontendLabel(),
            );
        }
        return $result;
    }

    /**
     * Configurable attribute suggestion action URL
     *
     * @return string
     */
    public function getSuggestUrl()
    {
        return $this->getUrl('*/catalog_product_attribute/suggestConfigurableAttributes');
    }
}
