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
     * @param $collection
     * @param \Magento\Catalog\Model\Category $category
     */
    public function filter(
        $collection,
        \Magento\Catalog\Model\Category $category
    );
}
