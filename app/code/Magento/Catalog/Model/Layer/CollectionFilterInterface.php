<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer;

interface CollectionFilterInterface
{
    /**
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @param $category
     */
    public function filter(
        \Magento\Catalog\Model\Resource\Product\Collection $collection,
        \Magento\Catalog\Model\Category $category
    );
}
