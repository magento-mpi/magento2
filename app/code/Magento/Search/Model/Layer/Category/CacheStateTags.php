<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer\Category;

class CacheStateTags
{
    public function getList(\Magento\Catalog\Model\Category $category, array $additionalTags = array())
    {
        return array_merge($additionalTags, array(
            \Magento\Catalog\Model\Category::CACHE_TAG . $category->getId(),
            \Magento\Catalog\Model\Category::CACHE_TAG . $category->getId() . '_SEARCH'
        ));
    }
} 
