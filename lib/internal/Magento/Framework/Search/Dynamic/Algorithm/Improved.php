<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic\Algorithm;

use Magento\Framework\Search\Dynamic\Algorithm;

class Improved implements AlgorithmInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var Algorithm
     */
    private $algorithm;

    /**
     * @param Algorithm $algorithm
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(Algorithm $algorithm, DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->algorithm = $algorithm;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $entityIds, array $intervals)
    {
        $aggregations = $this->dataProvider->getAggregations($entityIds);

        $options = $this->dataProvider->getOptions();
        if ($intervals && ($aggregations['count'] <= $options['interval_division_limit']
                || $intervals[0] == $intervals[1] || $intervals[1] === '0') || $aggregations['count'] <= 0
        ) {
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
 