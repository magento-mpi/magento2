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
    public function build(Select $baseQuery, RequestBucketInterface $bucket, array $entityIds)
    {
        $metrics = $this->metricsBuilder->build($bucket);

        $baseQuery->where('main_table.entity_id IN (?)', $entityIds);
        $baseQuery->columns($metrics);
        $baseQuery->group(RequestBucketInterface::FIELD_VALUE);

        return $baseQuery;
    }
}
