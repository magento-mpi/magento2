<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Dynamic\Algorithm;
use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface;

class Improved implements AlgorithmInterface
{
    /**
     * @var Algorithm
     */
    private $algorithm;

    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @param DataProviderInterface $dataProvider
     * @param Algorithm $algorithm
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        Algorithm $algorithm
    ) {
        $this->algorithm = $algorithm;
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(BucketInterface $bucket, array $dimensions, array $entityIds)
    {
        $aggregations = $this->dataProvider->getAggregations($entityIds);

        $options = $this->dataProvider->getOptions();
        if ($aggregations['count'] < $options['interval_division_limit']) {
            return [];
        }
        $this->algorithm->setStatistics(
            $aggregations['min'],
            $aggregations['max'],
            $aggregations['std'],
            $aggregations['count']
        );

        $interval = $this->dataProvider->getInterval($bucket, $dimensions, $entityIds);
        return $this->algorithm->calculateSeparators($interval);
    }
}
