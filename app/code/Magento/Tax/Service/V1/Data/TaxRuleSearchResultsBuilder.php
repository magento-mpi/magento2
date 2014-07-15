<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Service\Data\ObjectFactory;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
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
     * @param ObjectFactory $objectFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaxRuleBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TaxRuleBuilder $itemObjectBuilder
    ) {
        parent::__construct($objectFactory, $searchCriteriaBuilder, $itemObjectBuilder);
    }

    /**
     * Set tax rule items
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRule[] $taxRuleItems
     * @return $this
     */
    public function setItems($taxRuleItems)
    {
        return parent::setItems($taxRuleItems);
    }
}
