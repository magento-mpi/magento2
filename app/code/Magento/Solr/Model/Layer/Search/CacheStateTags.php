<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Solr\Model\Layer\Search;

class CacheStateTags extends \Magento\Solr\Model\Layer\Category\CacheStateTags
{
    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param array $additionalTags
     * @return array|void
     */
    public function getList(\Magento\Catalog\Model\Category $category, array $additionalTags = [])
    {
        $tags = parent::getList($category, $additionalTags);
        $tags[] = \Magento\Search\Model\Query::CACHE_TAG;
        return $tags;
    }
}
