<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\AbstractSearchResultsBuilder;

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
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerDetailsBuilder $customerDetailsObjectBuilder
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerDetailsBuilder $customerDetailsObjectBuilder
    ) {
        parent::__construct($searchCriteriaBuilder, $customerDetailsObjectBuilder);
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
