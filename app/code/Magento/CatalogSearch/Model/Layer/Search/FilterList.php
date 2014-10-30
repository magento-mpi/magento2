<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Layer\Search;

use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{
    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param FilterableAttributeListInterface $filterableAttributes
     * @param array $filters
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        FilterableAttributeListInterface $filterableAttributes,
        array $filters = array()
    ) {
        parent::__construct($objectManager, $filterableAttributes, $filters);
    }
}
