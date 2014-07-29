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
 * Builder for the TaxClassSearchResults Service Data Object
 *
 * @method TaxClassSearchResults create()
 */
class TaxClassSearchResultsBuilder extends AbstractSearchResultsBuilder
{
    /**
     * Constructor
     *
     * @param ObjectFactory $objectFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaxClassBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TaxClassBuilder $itemObjectBuilder
    ) {
        parent::__construct($objectFactory, $searchCriteriaBuilder, $itemObjectBuilder);
    }

    /**
     * Set tax class items
     *
     * @param \Magento\Tax\Service\V1\Data\TaxClass[] $taxClassItems
     * @return $this
     */
    public function setItems($taxClassItems)
    {
        return parent::setItems($taxClassItems);
    }
}
