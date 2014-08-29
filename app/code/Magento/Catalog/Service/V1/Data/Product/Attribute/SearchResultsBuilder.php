<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\Product\Attribute;

use Magento\Framework\Service\Data\ObjectFactory;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\AbstractSearchResultsBuilder;
use Magento\Catalog\Service\V1\Data\Eav\AttributeBuilder;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResults create()
 * @codeCoverageIgnore
 */
class SearchResultsBuilder extends \Magento\Framework\Service\V1\Data\AbstractSearchResultsBuilder
{
    /**
     * Constructor
     *
     * @param ObjectFactory $objectFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AttributeBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AttributeBuilder $itemObjectBuilder
    ) {
        parent::__construct($objectFactory, $searchCriteriaBuilder, $itemObjectBuilder);
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
