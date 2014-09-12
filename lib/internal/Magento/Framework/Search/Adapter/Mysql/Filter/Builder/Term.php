<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

use Magento\Framework\App\Resource;

class Term implements FilterInterface
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
     * {@inheritdoc}
     */
    public function buildFilter(
        \Magento\Framework\Search\Request\FilterInterface $filter
    ) {
        $adapter = $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);

        /** @var \Magento\Framework\Search\Request\Filter\Term $filter */
        $condition = sprintf(
            '%s = %s',
            $filter->getField(),
            $adapter->quote($filter->getValue())
        );
        return $condition;
    }
}
