<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Storage;

use Magento\CatalogUrlRewrite\Model\Resource\Category\Product;
use Magento\UrlRewrite\Model\Storage\DbStorage as BaseDbStorage;
use Magento\UrlRewrite\Service\V1\Data\Filter;

class DbStorage extends BaseDbStorage
{
    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\Filter $filter
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelect($filter)
    {
        $data = $filter->getData();
        $select = $this->connection->select();
        $select->from(array('url_rewrite' => $this->resource->getTableName('url_rewrite')))
            ->joinLeft(
                array('relation' => $this->resource->getTableName(Product::TABLE_NAME)),
                'url_rewrite.url_rewrite_id = relation.url_rewrite_id'
            )
            ->where('url_rewrite.entity_id = ?', $data['entity_id'])
            ->where('url_rewrite.entity_type = ?', $data['entity_type'])
            ->where('url_rewrite.store_id = ?', $data['store_id']);
        if (!empty($data[Filter::FIELD_DATA]['category_id'])) {
            $select->where('relation.category_id IN (?)', $data[Filter::FIELD_DATA]['category_id']);
        } else {
            $select->where('relation.category_id IS NULL');
        }
        return $select;
    }
}
