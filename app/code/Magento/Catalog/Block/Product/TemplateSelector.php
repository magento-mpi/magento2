<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product gallery
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product;

class TemplateSelector extends \Magento\Core\Block\Template
{
    /**
     * Retrieve list of product templates with search part contained in label
     *
     * @param string $labelPart
     * @return array
     */
    public function getSuggestedTemplates($labelPart)
    {
        $product = \Mage::registry('product');
        $entityType = $product->getResource()->getEntityType();
        $labelPart = \Mage::getResourceHelper('Magento_Core')->addLikeEscape($labelPart, array('position' => 'any'));
        $collection = \Mage::getResourceModel('\Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection')
            ->setEntityTypeFilter($entityType->getId())
            ->addFieldToFilter('attribute_set_name', array('like' => $labelPart))
            ->addFieldToSelect('attribute_set_id', 'id')
            ->addFieldToSelect('attribute_set_name', 'label')
            ->setOrder(
                'attribute_set_name',
                \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection::SORT_ORDER_ASC
            );
        return $collection->getData();
    }
}
