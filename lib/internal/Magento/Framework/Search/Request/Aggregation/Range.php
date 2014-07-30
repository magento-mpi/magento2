<?php
/**
 * Range Buckets
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Aggregation;

use Magento\Framework\Search\Request\BucketInterface;

class Range implements BucketInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var array
     */
    protected $metrics;

    /**
     * @var string
     */
    protected $ranges;

    /**
     * @param string $name
     * @param string $field
     * @param array $metrics
     * @param array $ranges
     */
    public function __construct($name, $field, array $metrics, array $ranges)
    {
        $this->name = $name;
        $this->field = $field;
        $this->metrics = $metrics;
        $this->ranges = $ranges;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return BucketInterface::TYPE_RANGE;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get Metrics
     *
     * @return array
     */
    public function getMetrics()
    {
        return $this->metrics;
    }

    /**
     * Get Ranges
     *
     * @return int
     */
    public function getRanges()
    {
        return $this->ranges;
    }
}
