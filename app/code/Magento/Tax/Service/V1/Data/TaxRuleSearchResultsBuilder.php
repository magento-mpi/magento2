<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Framework\Service\V1\Data\AbstractSearchResultsBuilder;

/**
 * Builder for the TaxRuleSearchResults Service Data Object
 *
 * @method TaxRuleSearchResults create()
 */
class TaxRuleSearchResultsBuilder extends AbstractSearchResultsBuilder
{
    /**
     * Constructor
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaxRuleBuilder $itemObjectBuilder
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TaxRuleBuilder $itemObjectBuilder
    ) {
        parent::__construct($searchCriteriaBuilder, $itemObjectBuilder);
    }

    /**
     * Set items
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRule[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return parent::setItems($items);
    }
}
