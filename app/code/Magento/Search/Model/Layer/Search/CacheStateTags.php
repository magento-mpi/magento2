<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;


class CacheStateTags extends \Magento\Search\Model\Layer\Category\CacheStateTags
{
    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param array $additionalTags
     * @return array|void
     */
    public function getList(\Magento\Catalog\Model\Category $category, array $additionalTags = array())
    {
        $tags = parent::getList($category, $additionalTags);
        $tags[] = \Magento\CatalogSearch\Model\Query::CACHE_TAG;
        return $tags;
    }
}
