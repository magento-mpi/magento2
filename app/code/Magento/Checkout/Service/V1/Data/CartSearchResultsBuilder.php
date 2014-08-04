<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

use Magento\Framework\Service\Data\ObjectFactory;
use Magento\Framework\Service\V1\Data\SearchCriteriaBuilder;
use Magento\Framework\Service\V1\Data\AbstractSearchResultsBuilder;

class CartSearchResultsBuilder extends AbstractSearchResultsBuilder
{
    /**
     * @param ObjectFactory $objectFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CartBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CartBuilder $itemObjectBuilder
    ) {
        parent::__construct($objectFactory, $searchCriteriaBuilder, $itemObjectBuilder);
    }

    /**
     * Set cart list
     *
     * @param \Magento\Checkout\Service\V1\Data\Cart[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return parent::setItems($items);
    }
}
