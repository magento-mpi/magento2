<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Search;


class FilterableAttributeList extends \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
{
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter()
            ->addVisibleFilter();
        return $collection;
    }
}
