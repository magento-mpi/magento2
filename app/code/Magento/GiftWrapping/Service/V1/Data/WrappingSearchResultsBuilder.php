<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Service\V1\Data;

use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\AbstractSearchResultsBuilder;
use Magento\Framework\Api\AttributeDataBuilder;
use Magento\Framework\Api\MetadataServiceInterface;

/**
 * @codeCoverageIgnore
 */
class WrappingSearchResultsBuilder extends AbstractSearchResultsBuilder
{

    /**
     * @param ObjectFactory $objectFactory
     * @param AttributeDataBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param WrappingBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeDataBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        WrappingBuilder $itemObjectBuilder
    ) {
        parent::__construct(
            $objectFactory,
            $valueBuilder,
            $metadataService,
            $searchCriteriaBuilder,
            $itemObjectBuilder
        );
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
