<?php
/**
 * Cache state tags list
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
    public function getList(\Magento\Catalog\Model\Category $category, array $additionalTags = array())
    {
        return array_merge(
            $additionalTags,
            array(
                \Magento\Catalog\Model\Category::CACHE_TAG . $category->getId(),
                \Magento\Catalog\Model\Category::CACHE_TAG . $category->getId() . '_SEARCH'
            )
        );
    }
}
