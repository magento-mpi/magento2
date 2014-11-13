<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

use Magento\Framework\Api\AttributeDataBuilder;
use Magento\Framework\Api\MetadataServiceInterface;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\AbstractSearchResultsBuilder;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method \Magento\Customer\Service\V1\Data\SearchResults create()
 */
class SearchResultsBuilder extends AbstractSearchResultsBuilder
{

    /**
     * Constructor
     *
     * @param ObjectFactory $objectFactory
     * @param AttributeDataBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerDetailsBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeDataBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerDetailsBuilder $itemObjectBuilder
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
     * Set customer details items
     *
     * @param \Magento\Customer\Service\V1\Data\CustomerDetails[] $customerDetailsItems
     * @return $this
     */
    public function setItems($customerDetailsItems)
    {
        return parent::setItems($customerDetailsItems);
    }
}
