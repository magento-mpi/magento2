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

class Dynamic implements BucketInterface
{
    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function build(Select $baseQuery, RequestBucketInterface $bucket, array $entityIds)
    {

    }
}
