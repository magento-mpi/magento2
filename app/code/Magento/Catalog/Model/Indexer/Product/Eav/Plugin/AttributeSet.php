<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Eav\Plugin;

class AttributeSet
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Eav\Processor
     */
    protected $_indexerEavProcessor;

    /**
     * @var \Magento\Catalog\Model\Resource\Eav\AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Eav\Processor $indexerEavProcessor
     * @param \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Eav\Processor $indexerEavProcessor,
        \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory
    ) {
        $this->_indexerEavProcessor = $indexerEavProcessor;
        $this->_attributeFactory = $attributeFactory;
    }

    /**
     * Reindex price for affected product
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Set $subject
     * @param callable $proceed
     *
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Eav\Model\Entity\Attribute\Set $subject,
        \Closure $proceed
    ) {
        if ($subject->getId()) {
            $originalSet = clone $subject;
            $originalSet->initFromSkeleton($subject->getId());
            $originalAttributeCodes = $this->_fetchIndexableAttributeCodesFromSet($originalSet);
            $subjectAttributeCodes = $this->_fetchIndexableAttributeCodesFromSet($subject);
            if (count(array_diff($originalAttributeCodes, $subjectAttributeCodes))) {
                $this->_indexerEavProcessor->markIndexerAsInvalid();
            }
        }
        $proceed();
        return $subject;
    }

    /**
     * Fetch indexable attribute models from set
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Set $set
     * @return array
     */
    protected function _fetchIndexableAttributesFromSet(\Magento\Eav\Model\Entity\Attribute\Set $set)
    {
        $catalogResource = $this->_attributeFactory->create();
        $attributes = [];
        foreach ($set->getGroups() as $group) {
            /** @var $group \Magento\Eav\Model\Entity\Attribute\Group */
            foreach ($group->getAttributes() as $attribute) {
                /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
                $catalogResource->clearInstance()->load($attribute->getId());
                if ($catalogResource->isIndexable()) {
                    $attributes[] = $attribute;
                }
            }
        }
        return $attributes;
    }

    /**
     * Fetch indexable attribute codes from set
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Set $set
     * @return array
     */
    protected function _fetchIndexableAttributeCodesFromSet(\Magento\Eav\Model\Entity\Attribute\Set $set)
    {
        $codes = [];
        foreach ($this->_fetchIndexableAttributesFromSet($set) as $attribute) {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute->load($attribute->getAttributeId());
            $codes[] = $attribute->getAttributeCode();
        }
        return $codes;
    }
}
