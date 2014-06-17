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
 * Builder for the TaxRateSearchResults Service Data Object
 *
 * @method TaxRateSearchResults create()
 */
class TaxRateSearchResultsBuilder extends AbstractSearchResultsBuilder
{
    /**
     * Constructor
     *
     * @param ObjectFactory $objectFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaxRateBuilder $taxRateObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TaxRateBuilder $taxRateObjectBuilder
    ) {
        parent::__construct($objectFactory, $searchCriteriaBuilder, $taxRateObjectBuilder);
    }

    /**
     * Set tax rule items
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRate[] $taxRateItems
     * @return $this
     */
    public function setItems($taxRateItems)
    {
        return parent::setItems($taxRateItems);
    }
}
