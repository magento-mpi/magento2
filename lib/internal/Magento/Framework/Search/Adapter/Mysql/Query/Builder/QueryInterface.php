<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Query\Builder;

use Magento\Framework\Search\Adapter\Mysql\ScoreBuilder;

interface QueryInterface
{
    /**
     * @param \Magento\Framework\Search\Adapter\Mysql\ScoreBuilder $scoreBuilder
     * @param \Magento\Framework\DB\Select $select
     * @param \Magento\Framework\Search\Request\QueryInterface $query
     * @param bool $isNot
     * @return \Magento\Framework\DB\Select
     */
    public function build(
        ScoreBuilder $scoreBuilder,
        \Magento\Framework\DB\Select $select,
        \Magento\Framework\Search\Request\QueryInterface $query,
        $isNot
    );
}
