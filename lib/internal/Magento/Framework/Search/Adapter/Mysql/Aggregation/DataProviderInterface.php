<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\Dimension;

interface DataProviderInterface
{
    /**
     * @param BucketInterface $bucket
     * @param Dimension[] $dimensions
     * @return Select
     */
    public function getDataSet(BucketInterface $bucket, array $dimensions);

    /**
     * Executes query and return raw response
     *
     * @param Select $select
     * @return array
     */
    public function execute(Select $select);
}
