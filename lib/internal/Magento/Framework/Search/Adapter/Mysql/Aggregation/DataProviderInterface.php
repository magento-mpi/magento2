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
use Magento\Framework\Search\RequestInterface;

interface DataProviderInterface
{
    /**
     * @param BucketInterface $bucket
     * @param RequestInterface $request
     * @return Select
     */
    public function getDataSet(BucketInterface $bucket, RequestInterface $request);
}
