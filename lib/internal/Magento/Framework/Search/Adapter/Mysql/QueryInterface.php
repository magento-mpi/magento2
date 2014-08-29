<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

interface QueryInterface
{
    /**
     * @param \Magento\Framework\DB\Select $select
     * @param \Magento\Framework\Search\Request\QueryInterface $query
     * @param string $queryType
     * @return \Magento\Framework\DB\Select
     */
    public function buildQuery(
        \Magento\Framework\DB\Select $select,
        \Magento\Framework\Search\Request\QueryInterface $query,
        $queryType
    );
}
