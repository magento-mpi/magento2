<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\Product\Attribute;

use Magento\Catalog\Service\V1\Data\Eav\AttributeBuilder;
use Magento\Framework\Service\Data\AttributeValueBuilder;
use Magento\Framework\Service\Data\MetadataServiceInterface;
use Magento\Framework\Service\Data\ObjectFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResults create()
 * @codeCoverageIgnore
 */
class SearchResultsBuilder extends \Magento\Framework\Api\AbstractSearchResultsBuilder
{
    /**
     * Constructor
     *
     * @param ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AttributeBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AttributeBuilder $itemObjectBuilder
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
     * Set items
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\Attribute[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return parent::setItems($items);
    }
}
