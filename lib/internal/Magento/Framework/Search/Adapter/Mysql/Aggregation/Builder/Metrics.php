<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder;

use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;

class Metrics
{
    /**
     * Available metrics
     *
     * @var string[]
     */
    private $mapMetrics = ['count', 'sum', 'min', 'max', 'avg'];

    /**
     * Build methics for Select->columns
     *
     * @param RequestBucketInterface $bucket
     * @return string[]
     */
    public function build(RequestBucketInterface $bucket)
    {
        $selectAggregations = [];
        /** @var \Magento\Framework\Search\Request\Aggregation\Metric[] $metrics */
        $metrics = $bucket->getMetrics();

        foreach ($metrics as $metric) {
            $metricType = $metric->getType();
            if (in_array($metricType, $this->mapMetrics)) {
                $selectAggregations[$metricType] = "$metricType(main_table.value)";
            }
        }

        return $selectAggregations;
    }
}
