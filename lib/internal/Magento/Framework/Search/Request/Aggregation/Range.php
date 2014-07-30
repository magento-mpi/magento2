<?php
/**
 * Range
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request\Aggregation;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class Range
{
    /**
     * @var int|null
     */
    protected $from;

    /**
     * @var int|null
     */
    protected $to;

    /**
     * @param int|null $from
     * @param int|null $to
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Get From
     *
     * @return int|null
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Get To
     *
     * @return int|null
     */
    public function getTo()
    {
        return $this->to;
    }
}
