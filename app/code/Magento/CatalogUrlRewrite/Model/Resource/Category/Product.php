<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Resource\Category;

use Magento\Framework\Model\Resource\Db\AbstractDb;
use Magento\UrlRewrite\Service\V1\Data\FilterInterface;

class Product extends AbstractDb
{
    /**
     * Product/Category relation table name
     */
    const TABLE_NAME = 'url_rewrite_relation';

    /**
     * Chunk for mass insert
     */
    const CHUNK_SIZE = 100;

    /**
     * Primary key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'url_rewrite_id');
    }

    /**
     * @param FilterInterface $filter
     * @return array
     */
    public function findByFilter(FilterInterface $filter)
    {
        $select = $this->prepareSelect($filter);

        return $this->getReadConnection()->fetchRow($select);
    }

    /**
     * @param FilterInterface $filter
     * @return array
     */
    public function findAllByFilter(FilterInterface $filter)
    {
        $select = $this->prepareSelect($filter);

        return $this->getReadConnection()->fetchAll($select);
    }

    /**
     * @param FilterInterface $filter
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelect(FilterInterface $filter)
    {
        $data = $filter->getFilter();
        $connection = $this->getReadConnection();
        $select = clone $connection->select();
        $select->reset()
            ->from(array('url_rewrite' => $this->getTable('url_rewrite')))
            ->joinLeft(
                array('relation' => $this->getTable(self::TABLE_NAME)),
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

    /**
     * @param array $insertData
     * @return int
     */
    public function saveMultiple(array $insertData)
    {
        $write = $this->_getWriteAdapter();
        if (sizeof($insertData) <= self::CHUNK_SIZE) {
            return $write->insertMultiple($this->getTable(self::TABLE_NAME), $insertData);
        }
        $data = array_chunk($insertData, self::CHUNK_SIZE);
        $totalCount = 0;
        foreach ($data as $insertData) {
            $totalCount += $write->insertMultiple($this->getTable(self::TABLE_NAME), $insertData);
        }
        return $totalCount;
    }

    /**
     * @param array $removeData
     * @return int
     */
    public function removeMultiple(array $removeData)
    {
        $write = $this->_getWriteAdapter();
        return $write->delete($this->getTable(self::TABLE_NAME), array('url_rewrite_id in (?)' => $removeData));
    }
}
