<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

interface FilterInterface
{
    /**
     * @param \Magento\Framework\Search\Request\FilterInterface $filter
     * @return \Magento\Framework\DB\Select
     */
    public function buildFilter(
        \Magento\Framework\Search\Request\FilterInterface $filter
    );
}
