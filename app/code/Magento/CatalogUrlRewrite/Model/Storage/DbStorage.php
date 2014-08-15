<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Storage;

use Magento\UrlRewrite\Model\Storage\DbStorage as UrlRewriteDbStorage;
use Magento\UrlRewrite\Model\Storage\DuplicateEntryException;
use Magento\UrlRewrite\Service\V1\Data\Filter;
use Magento\CatalogUrlRewrite\Model\Resource\Category\Product;

class DbStorage extends UrlRewriteDbStorage
{
    /**
     * {@inheritdoc}
     */
    protected function doFindAllByFilter($filter)
    {
        $select = $this->prepareSelectByData($filter->getFilter());

        return $this->connection->fetchAll($select);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindByFilter($filter)
    {
        $select = $this->prepareSelectByData($filter->getFilter());

        return $this->connection->fetchRow($select);
    }

    /**
     * {@inheritdoc}
     */
    public function findByData(array $data)
    {
        $select = $this->prepareSelectByData($data);
        $row = $this->connection->fetchRow($select);

        return $row ? $this->createUrlRewrite($row) : null;
    }

    /**
     * @param array $data
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelectByData(array $data)
    {
        $select = clone $this->connection->select();
        $select->reset()
            ->from(array('url_rewrite' => $this->resource->getTableName('url_rewrite')))
            ->joinLeft(
                array('relation' => $this->resource->getTableName(Product::TABLE_NAME)),
                'url_rewrite.url_rewrite_id = relation.url_rewrite_id'
            )
            ->where('url_rewrite.entity_id = ?', $data['entity_id'])
            ->where('url_rewrite.entity_type = ?', $data['entity_type'])
            ->where('url_rewrite.store_id = ?', $data['store_id']);
        if (!empty($data['category_id'])) {
            $select->where('relation.category_id IN (?)', $data['category_id']);
        } else {
            $select->where('relation.category_id is null');
        }
        return $select;
    }
}
 