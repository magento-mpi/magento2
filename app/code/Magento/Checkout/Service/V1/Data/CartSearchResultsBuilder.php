<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

use Magento\Framework\Api\AttributeDataBuilder;
use Magento\Framework\Api\MetadataServiceInterface;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\AbstractSearchResultsBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * @codeCoverageIgnore
 */
class CartSearchResultsBuilder extends AbstractSearchResultsBuilder
{
    /**
     * Constructor
     *
     * @param ObjectFactory $objectFactory
     * @param AttributeDataBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CartBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeDataBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CartBuilder $itemObjectBuilder
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
     * Set cart list
     *
     * @param \Magento\Checkout\Service\V1\Data\Cart[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return parent::setItems($items);
    }
}
