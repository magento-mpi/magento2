<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

interface FilterInterface
{
    /**
     * @param RequestFilterInterface $filter
     * @param bool $isNegation
     * @return \Magento\Framework\DB\Select
     */
    public function buildFilter(
        RequestFilterInterface $filter,
        $isNegation
    );
}
