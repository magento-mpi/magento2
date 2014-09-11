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
use Magento\Framework\Search\Request\Filter\Range as RangeFilterRequest;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

class Range implements FilterInterface
{
    const CONDITION_PART_GREATER_THAN = '>=';
    const CONDITION_PART_LOWER_THAN = '<';
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
     * {@inheritdoc}
     */
    public function buildFilter(
        RequestFilterInterface $filter,
        $isNegation
    ) {
        $adapter = $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
        /** @var RangeFilterRequest $filter */
        return $this->generateCondition(
            $filter,
            $isNegation,
            $adapter
        );
    }

    /**
     * @param RequestFilterInterface|RangeFilterRequest $filter
     * @param bool $isNegation
     * @param AdapterInterface $adapter
     * @return string
     */
    private function generateCondition(RequestFilterInterface $filter, $isNegation, AdapterInterface $adapter)
    {
        $leftPart = $this->generateConditionLeftPart($filter, $isNegation, $adapter);
        $rightPart = $this->generateConditionRightPart($filter, $isNegation, $adapter);
        $unionOperator = $this->generateConditionUnionOperator($leftPart, $rightPart, $isNegation);

        return $leftPart . $unionOperator . $rightPart;
    }

    /**
     * @param RequestFilterInterface|RangeFilterRequest $filter
     * @param bool $isNegation
     * @param AdapterInterface $adapter
     * @return string
     */
    private function generateConditionLeftPart(RequestFilterInterface $filter, $isNegation, AdapterInterface $adapter)
    {
        $condition = '';
        if (!is_null($filter->getFrom())) {
            $condition = sprintf(
                '%s %s %s',
                $filter->getField(),
                ($isNegation ? self::CONDITION_PART_LOWER_THAN : self::CONDITION_PART_GREATER_THAN),
                $adapter->quote($filter->getFrom())
            );
        }
        return $condition;
    }

    /**
     * @param RequestFilterInterface|RangeFilterRequest $filter
     * @param bool $isNegation
     * @param AdapterInterface $adapter
     * @return string
     */
    private function generateConditionRightPart(RequestFilterInterface $filter, $isNegation, AdapterInterface $adapter)
    {
        $condition = '';
        if (!is_null($filter->getTo())) {
            $condition = sprintf(
                '%s %s %s',
                $filter->getField(),
                ($isNegation ? self::CONDITION_PART_GREATER_THAN : self::CONDITION_PART_LOWER_THAN),
                $adapter->quote($filter->getTo())
            );
        }
        return $condition;
    }

    /**
     * @param string $leftPart
     * @param string $rightPart
     * @param bool $isNegation
     * @return string
     */
    private function generateConditionUnionOperator($leftPart, $rightPart, $isNegation)
    {
        $condition = '';
        if (!empty($leftPart) and !empty($rightPart)) {
            $condition = ' ' . ($isNegation ? \Zend_Db_Select::SQL_OR : \Zend_Db_Select::SQL_AND) . ' ';
        }
        return $condition;
    }
}
