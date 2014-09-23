<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Response\Aggregation;

class Value
{
    /**
     * @var string|array
     */
    private $value;

    /**
     * @var array
     */
    private $metrics;

    /**
     * @param $value
     * @param $metrics
     */
    public function __construct($value, $metrics)
    {
        $this->value = $value;
        $this->metrics = $metrics;
    }

    /**
     * Get aggregation
     * return string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getMetrics()
    {
        return $this->metrics;
    }
} 