<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;

interface BucketInterface
{
    /**
     * @param Select $select
     * @param RequestBucketInterface $bucket
     * @param array $productIds
     * @return mixed
     */
    public function build(Select $select, RequestBucketInterface $bucket, array $productIds);
}
