<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

use Magento\Framework\App\Resource;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

class Term implements FilterInterface
{
    const CONDITION_PART_EQUALS = '=';
    const CONDITION_PART_NOT_EQUALS = '!=';
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

        /** @var \Magento\Framework\Search\Request\Filter\Term $filter */
        $condition = sprintf(
            '%s %s %s',
            $filter->getField(),
            ($isNegation ? self::CONDITION_PART_NOT_EQUALS : self::CONDITION_PART_EQUALS),
            $adapter->quote($filter->getValue())
        );
        return $condition;
    }
}
