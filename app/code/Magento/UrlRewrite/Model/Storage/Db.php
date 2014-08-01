<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model\Storage;

use Magento\Framework\App\Resource;
// TODO: structure layer knows about service layer(and version) (@TODO: UrlRewrite)
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\UrlRewrite\Service\V1\Data\Filter;

/**
 * Db storage
 */
class Db extends AbstractStorage
{
    /**
     * DB Storage table name
     */
    const TABLE_NAME = 'url_rewrite';

    /**
     * Code of "Integrity constraint violation: 1062 Duplicate entry" error
     */
    const ERROR_CODE_DUPLICATE_ENTRY = 23000;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @param Converter $converter
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(Converter $converter, Resource $resource)
    {
        $this->connection = $resource->getConnection(Resource::DEFAULT_WRITE_RESOURCE);

        parent::__construct($converter);
    }

    /**
     * Prepare select statement for specific filter
     *
     * @param Filter $filter
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelect($filter)
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
    protected function doFindAllByFilter($filter)
    {
        return $this->connection->fetchAll($this->prepareSelect($filter));
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindByFilter($filter)
    {
        return $this->connection->fetchRow($this->prepareSelect($filter));
    }

    /**
     * {@inheritdoc}
     */
    protected function doAddMultiple($data)
    {
        try {
            $this->connection->insertMultiple(self::TABLE_NAME, $data);
        } catch (\Exception $e) {
            if ($e->getCode() === self::ERROR_CODE_DUPLICATE_ENTRY) {
                throw new DuplicateEntryException();
            }
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByFilter(Filter $filter)
    {
        $this->connection->query($this->prepareSelect($filter)->deleteFromSelect(self::TABLE_NAME));
    }
}
