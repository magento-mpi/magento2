<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Request\Dimension;

interface BucketInterface
{
    /**
     * @param DataProviderInterface $dataProvider
     * @param Dimension[] $dimensions
     * @param RequestBucketInterface $bucket
     * @param array $entityIds
     * @return array
     */
    public function build(
        DataProviderInterface $dataProvider,
        array $dimensions,
        RequestBucketInterface $bucket,
        array $entityIds
    );
}
