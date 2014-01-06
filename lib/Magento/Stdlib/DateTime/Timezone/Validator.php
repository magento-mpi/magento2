<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    \Magento\Stdlib
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Stdlib\DateTime\Timezone;

class Validator
{
    /**
     * Maximum allowed year value
     *
     * @var int
     */
    protected $_yearMaxValue;

    /**
     * Minimum allowed year value
     *
     * @var int
     */
    protected $_yearMinValue;

    public function __construct(
        $yearMinValue = \Magento\Stdlib\DateTime::YEAR_MIN_VALUE,
        $yearMaxValue = \Magento\Stdlib\DateTime::YEAR_MAX_VALUE
    )
    {
        $this->_yearMaxValue = $yearMaxValue;
        $this->_yearMinValue = $yearMinValue;
    }

    /**
     * Validate timestamp
     *
     * @param int|string $timestamp
     * @param int|string $toDate
     * @throws ValidationException
     */
    public function validate($timestamp, $toDate)
    {
        $transitionYear = date('Y', $timestamp);

        if ($transitionYear > $this->_yearMaxValue || $transitionYear < $this->_yearMinValue) {
            throw new ValidationException('Transition year is out of system date range.');
        }

        if ((int)$timestamp > (int)$toDate) {
            throw new ValidationException('Transition year is out of specified date range.');
        }
    }
}
