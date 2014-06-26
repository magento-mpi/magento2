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
     * @var AttributeSet\IndexableAttributeFilter
     */
    protected $_attributeFilter;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Eav\Processor $indexerEavProcessor
     * @param AttributeSet\IndexableAttributeFilter $filter
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Eav\Processor $indexerEavProcessor,
        AttributeSet\IndexableAttributeFilter $filter
    ) {
        $this->_indexerEavProcessor = $indexerEavProcessor;
        $this->_attributeFilter = $filter;
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
    public function aroundSave(\Magento\Eav\Model\Entity\Attribute\Set $subject, \Closure $proceed)
    {
        if ($subject->getId()) {
            $originalSet = clone $subject;
            $originalSet->initFromSkeleton($subject->getId());
            $originalAttributeCodes = $this->_attributeFilter->filter($originalSet);
            $subjectAttributeCodes = $this->_attributeFilter->filter($subject);
            if (count(array_diff($originalAttributeCodes, $subjectAttributeCodes))) {
                $this->_indexerEavProcessor->markIndexerAsInvalid();
            }
        }
        $proceed();
        return $subject;
    }
}
