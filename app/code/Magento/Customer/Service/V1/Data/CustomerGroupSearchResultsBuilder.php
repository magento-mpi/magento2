<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\AbstractSearchResultsBuilder;
use Magento\Framework\Api\AttributeValueBuilder;
use Magento\Framework\Api\MetadataServiceInterface;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method \Magento\Customer\Service\V1\Data\CustomerGroupSearchResults create()
 */
class CustomerGroupSearchResultsBuilder extends AbstractSearchResultsBuilder
{
    /**
     * Constructor
     *
     * @param ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerDetailsBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
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
     * @param \Magento\Customer\Service\V1\Data\CustomerGroup[] $customerGroupItems
     * @return $this
     */
    public function setItems($customerGroupItems)
    {
        return parent::setItems($customerGroupItems);
    }
}
