<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Eav\Plugin\AttributeSet;


class IndexableAttributeFilter
{
    /**
     * @var \Magento\Catalog\Model\Resource\Eav\AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @param \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory
     */
    public function __construct(\Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory)
    {
        $this->_attributeFactory = $attributeFactory;
    }

    /**
     * Retrieve codes of indexable attributes from given attribute set
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Set $set
     * @return array
     */
    public function filter(\Magento\Eav\Model\Entity\Attribute\Set $set)
    {
        $codes = [];
        $catalogResource = $this->_attributeFactory->create();

        foreach ($set->getGroups() as $group) {
            /** @var $group \Magento\Eav\Model\Entity\Attribute\Group */
            foreach ($group->getAttributes() as $attribute) {
                /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
                $catalogResource->load($attribute->getId());
                if ($catalogResource->isIndexable()) {
                    // Attribute requires to be cloned for new dataset to maintain attribute set changes
                    $attributeClone = clone $attribute;
                    $attributeClone->load($attribute->getAttributeId());
                    $codes[] = $attributeClone->getAttributeCode();
                    unset($attributeClone);
                }
            }
        }
        return $codes;
    }
}
