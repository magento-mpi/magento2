<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;

class ConditionManager
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->adapter = $resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }

    /**
     * @param string $query
     * @return string
     */
    public function wrapBrackets($query)
    {
        return empty($query)
            ? $query
            : '(' . $query . ')';
    }

    /**
     * @param string[] $queries
     * @param string $unionOperator
     * @return string
     */
    public function combineQueries(array $queries, $unionOperator)
    {
        return implode(
            ' ' . $unionOperator . ' ',
            array_filter($queries, 'strlen')
        );
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    public function generateCondition($field, $operator, $value)
    {
        return sprintf(
            '%s %s %s',
            $this->adapter->quoteIdentifier($field),
            $operator,
            $this->adapter->quote($value)
        );
    }
}
