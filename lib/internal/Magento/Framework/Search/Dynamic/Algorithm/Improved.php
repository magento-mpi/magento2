<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Dynamic\Algorithm;
use Magento\Framework\Search\Request\BucketInterface;

class Improved extends AbstractAlgorithm
{

    /**
     * @var Algorithm
     */
    private $algorithm;

    /**
     * @param DataProviderInterface $dataProvider
     * @param Algorithm $algorithm
     */
    public function __construct(DataProviderInterface $dataProvider, Algorithm $algorithm)
    {
        parent::__construct($dataProvider);
        $this->algorithm = $algorithm;
    }


    /**
     * {@inheritdoc}
     */
    public function getItems(BucketInterface $bucket, array $dimensions, array $entityIds)
    {
        $aggregations = $this->dataProvider->getAggregations($entityIds);

        $options = $this->dataProvider->getOptions();
        if ($aggregations['count'] < $options['interval_division_limit'] ) { // minimum 2 intervals
            return [];
        }

        $this->algorithm->setStatistics(
            $aggregations['min'],
            $aggregations['max'],
            $aggregations['std'],
            $aggregations['count']
        );

        if (!empty($intervals)) {
            $this->algorithm->setLimits($intervals[0], $intervals[1]);
        }

        return $this->algorithm->calculateSeparators();
    }
}
