<?php
/**
 * Cache state tags list
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Layer\Category;

class CacheStateTags
{
    /**
     * Retrieve list of cache state tags for given category
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param array $additionalTags
     * @return array
     */
    public function getList(\Magento\Catalog\Model\Category $category, array $additionalTags = [])
    {
        return array_merge(
            $additionalTags,
            [
                \Magento\Catalog\Model\Category::CACHE_TAG . $category->getId(),
                \Magento\Catalog\Model\Category::CACHE_TAG . $category->getId() . '_SEARCH'
            ]
        );
    }
}
