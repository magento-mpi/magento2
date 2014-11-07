<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Api\AttributeValueBuilder;
use Magento\Framework\Api\MetadataServiceInterface;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\AbstractSearchResultsBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;

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
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaxRateBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TaxRateBuilder $itemObjectBuilder
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
     * Set tax rate items
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRate[] $taxRateItems
     * @return $this
     */
    public function setItems($taxRateItems)
    {
        return parent::setItems($taxRateItems);
    }
}
