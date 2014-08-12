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
    const TABLE_NAME = 'url_rewrite_relation';

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

    public function findByFilter(FilterInterface $filter)
    {
        $select = $this->prepareSelect($filter);

        return $this->getReadConnection()->fetchRow($select);
    }

    public function findAllByFilter(FilterInterface $filter)
    {
        $select = $this->prepareSelect($filter);

        return $this->getReadConnection()->fetchAll($select);
    }

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
}
