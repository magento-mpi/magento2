<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Service\V1\Storage\Adapter;

use Magento\UrlRedirect\Service\V1\Storage\AdapterInterface;
use Magento\UrlRedirect\Service\V1\Storage\Data\Filter;
use Magento\Framework\App\Resource;

/**
 * Db storage adapter
 */
class Db implements AdapterInterface
{
    /**
     * DB Storage table name
     */
    const TABLE_NAME = 'url_rewrite';

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->connection = $resource->getConnection(Resource::DEFAULT_WRITE_RESOURCE);
    }

    /**
     * Prepare select statement for specific filter
     *
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Filter $filter
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelect(Filter $filter)
    {
        $select = $this->connection->select();
        $select->from(self::TABLE_NAME);

        foreach ($filter->getFilter() as $column => $value) {
            $select->where($this->connection->quoteIdentifier($column) . ' IN (?)', $value);
        }
        return $select;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(Filter $filter)
    {
        $select = $this->prepareSelect($filter);
        return $this->connection->fetchAll($select);
    }

    /**
     * {@inheritdoc}
     */
    public function find(Filter $filter)
    {
        $select = $this->prepareSelect($filter);
        return $this->connection->fetchRow($select);
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $data)
    {
        return $this->connection->insertMultiple(self::TABLE_NAME, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Filter $filter)
    {
        $select = $this->prepareSelect($filter);
        return $this->connection->query($select->deleteFromSelect(self::TABLE_NAME));
    }
}
