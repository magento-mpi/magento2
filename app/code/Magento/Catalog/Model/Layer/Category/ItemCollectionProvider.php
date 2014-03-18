<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Category;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;

class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getCollection(\Magento\Catalog\Model\Category $category)
    {
        return $category->getProductCollection();
    }
}
