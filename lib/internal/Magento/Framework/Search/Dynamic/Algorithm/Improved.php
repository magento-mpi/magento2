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
use Magento\Framework\Search\Adapter\Mysql\Aggregation\IntervalFactory;
use Magento\Framework\Search\Request\BucketInterface;

class Improved extends AbstractAlgorithm
{
    /**
     * @var Algorithm
     */
    private $algorithm;

    /**
     * @var IntervalFactory
     */
    private $intervalFactory;

    /**
     * @param DataProviderInterface $dataProvider
     * @param Algorithm $algorithm
     * @param IntervalFactory $intervalFactory
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        Algorithm $algorithm,
        IntervalFactory $intervalFactory
    ) {
        parent::__construct($dataProvider);
        $this->algorithm = $algorithm;
        $this->intervalFactory = $intervalFactory;
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
        $select = $this->dataProvider->getDataSet($bucket, $dimensions);
        $select->where('main_table.entity_id IN (?)', $entityIds);

        $interval = $this->intervalFactory->create(['select' => $select]);
        $this->algorithm->setStatistics(
            $aggregations['min'],
            $aggregations['max'],
            $aggregations['std'],
            $aggregations['count']
        );

        return $this->algorithm->calculateSeparators($interval);
    }
}
