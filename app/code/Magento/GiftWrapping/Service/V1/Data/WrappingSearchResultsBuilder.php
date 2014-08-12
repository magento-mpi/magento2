<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Service\V1\Data;

use Magento\Framework\Service\Data\ObjectFactory;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\AbstractSearchResultsBuilder;

class WrappingSearchResultsBuilder extends AbstractSearchResultsBuilder
{
    /**
     * @param ObjectFactory $objectFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param WrappingBuilder $wrappingBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        WrappingBuilder $wrappingBuilder
    ) {
        parent::__construct($objectFactory, $searchCriteriaBuilder, $wrappingBuilder);
    }

    /**
     * Set gift wrapping items
     *
     * @param \Magento\GiftWrapping\Service\V1\Data\Wrapping[] $wrappingItems
     * @return $this
     */
    public function setItems($wrappingItems)
    {
        return parent::setItems($wrappingItems);
    }
}
