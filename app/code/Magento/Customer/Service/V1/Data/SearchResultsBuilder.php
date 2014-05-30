<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method \Magento\Customer\Service\V1\Data\SearchResults create()
 */
class SearchResultsBuilder extends \Magento\Framework\Service\V1\Data\SearchResultsBuilder
{
    /**
     * Constructor
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerDetailsBuilder $itemObjectBuilder
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerDetailsBuilder $itemObjectBuilder
    ) {
        parent::__construct($searchCriteriaBuilder, $itemObjectBuilder);
    }

    /**
     * Set items
     *
     * @param \Magento\Customer\Service\V1\Data\CustomerDetails[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return parent::setItems($items);
    }
}
