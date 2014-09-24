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

class Term implements BucketInterface
{
    /**
     * @var Metrics
     */
    private $metricsBuilder;

    /**
     * @param Metrics $metricsBuilder
     */
    public function __construct(Metrics $metricsBuilder)
    {
        $this->metricsBuilder = $metricsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function build(Select $select, RequestBucketInterface $bucket, array $productIds)
    {
        $metrics = $this->metricsBuilder->build($bucket);

        $select->where('main_table.entity_id IN (?)', $productIds);
        $select->columns($metrics);
        $select->group('value');

        return $select;
    }
}
