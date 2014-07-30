<?php
/**
 * Range Filter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Filter;

use Magento\Framework\Search\Request\FilterInterface;

class Range implements FilterInterface
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
     * @var int
     */
    protected $from;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @param string $name
     * @param string $field
     * @param int $from
     * @param int $limit
     */
    public function __construct($name, $field, $from, $limit)
    {
        $this->name = $name;
        $this->field = $field;
        $this->from = $from;
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return FilterInterface::TYPE_RANGE;
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
     * Get From
     *
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Get Limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
