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
 * Product gallery
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_TemplateSelector extends Magento_Core_Block_Template
{
    /**
     * Retrieve list of product templates with search part contained in label
     *
     * @param string $labelPart
     * @return array
     */
    public function getSuggestedTemplates($labelPart)
    {
        $product = Mage::registry('product');
        $entityType = $product->getResource()->getEntityType();
        $labelPart = Mage::getResourceHelper('Magento_Core')->addLikeEscape($labelPart, array('position' => 'any'));
        $collection = Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection')
            ->setEntityTypeFilter($entityType->getId())
            ->addFieldToFilter('attribute_set_name', array('like' => $labelPart))
            ->addFieldToSelect('attribute_set_id', 'id')
            ->addFieldToSelect('attribute_set_name', 'label')
            ->setOrder('attribute_set_name', Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection::SORT_ORDER_ASC);
        return $collection->getData();
    }
}
