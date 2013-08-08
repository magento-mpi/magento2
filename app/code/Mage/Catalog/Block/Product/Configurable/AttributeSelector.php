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
class Mage_Catalog_Block_Product_Configurable_AttributeSelector extends Mage_Backend_Block_Template
{
    /**
     * Retrieve list of attributes with admin store label containing $labelPart
     *
     * @param string $labelPart
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getSuggestedAttributes($labelPart)
    {
        $escapedLabelPart = Mage::getResourceHelper('Magento_Core')
            ->addLikeEscape($labelPart, array('position' => 'any'));
        /** @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Attribute_Collection')
            ->addFieldToFilter('frontend_input', 'select')
            ->addFieldToFilter('frontend_label', array('like' => $escapedLabelPart))
            ->addFieldToFilter('is_configurable', 1)
            ->addFieldToFilter('is_user_defined', 1)
            ->addFieldToFilter('is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);

        $result = array();
        $types = array(
            Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
            Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
            Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        );
        foreach ($collection->getItems() as $id => $attribute) {
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if (!$attribute->getApplyTo() || count(array_diff($types, $attribute->getApplyTo())) === 0) {
                $result[$id] = array(
                    'id'      => $attribute->getId(),
                    'label'   => $attribute->getFrontendLabel(),
                    'code'    => $attribute->getAttributeCode(),
                    'options' => $attribute->getSource()->getAllOptions(false)
                );
            }
        }
        return $result;
    }

    /**
     * Attribute set creation action URL
     *
     * @return string
     */
    public function getAttributeSetCreationUrl()
    {
        return $this->getUrl('*/catalog_product_set/save');
    }

    /**
     * Get options for suggest widget
     *
     * @return array
     */
    public function getSuggestWidgetOptions()
    {
        return array(
            'source' => $this->getUrl('*/catalog_product_attribute/suggestConfigurableAttributes'),
            'minLength' => 0,
            'className' => 'category-select',
            'showAll' => true,
        );
    }
}
