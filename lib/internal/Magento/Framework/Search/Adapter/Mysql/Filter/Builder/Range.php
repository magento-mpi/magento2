<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Range implements FilterInterface
{
    /**
     * @var \Magento\Framework\App\Resource
     */
    private $resource;

    /**
     * @param \Magento\Framework\App\Resource $resource
     */
    public function __construct(\Magento\Framework\App\Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param \Magento\Framework\Search\Request\FilterInterface $filter
     * @return \Magento\Framework\DB\Select
     */
    public function buildFilter(
        \Magento\Framework\Search\Request\FilterInterface $filter
    ) {
        $adapter = $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
        /** @var \Magento\Framework\Search\Request\Filter\Range $filter */
        return $this->generateCondition($filter->getField(), $filter->getFrom(), $filter->getTo(), $adapter);
    }

    private function generateCondition($field, $from, $to, AdapterInterface $adapter)
    {
        $hasFromValue = !is_null($from);
        $hasToValue = !is_null($to);

        $condition = '';

        if ($hasFromValue and $hasToValue) {
            $condition = sprintf(
                '%s >= %s %s %s < %s',
                $field,
                $adapter->quote($from),
                \Zend_Db_Select::SQL_AND,
                $field,
                $adapter->quote($to)
            );
        } elseif ($hasFromValue and !$hasToValue) {
            $condition = sprintf(
                '%s >= %s',
                $field,
                $adapter->quote($from)
            );
        } elseif (!$hasFromValue and $hasToValue) {
            $condition = sprintf(
                '%s < %s',
                $field,
                $adapter->quote($to)
            );
        }
        return $condition;
    }
}
