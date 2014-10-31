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

class Improved implements AlgorithmInterface
{

    /**
     * @var Algorithm
     */
    private $algorithm;

    /**
     * @param Algorithm $algorithm
     */
    public function __construct(Algorithm $algorithm)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(DataProviderInterface $dataProvider, array $entityIds)
    {
        $aggregations = $dataProvider->getAggregations($entityIds);

        $options = $dataProvider->getOptions();
        if ($aggregations['count'] <= $options['interval_division_limit']) {
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
