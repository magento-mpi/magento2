<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    \Magento\Stdlib
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Stdlib;

/**
 * Converter of date formats
 * Internal dates
 */
class DateTime
{
    /**#@+
     * Date format, used as default. Compatible with \Zend_Date
     */
    const DATETIME_INTERNAL_FORMAT = 'yyyy-MM-dd HH:mm:ss';
    const DATE_INTERNAL_FORMAT     = 'yyyy-MM-dd';

    const DATETIME_PHP_FORMAT       = 'Y-m-d H:i:s';
    const DATE_PHP_FORMAT           = 'Y-m-d';
    /**#@-*/

    /**
     * Convert date to UNIX timestamp
     * Returns current UNIX timestamp if date is true
     *
     * @param \Zend_Date|bool $date
     * @return int
     */
    public function toTimestamp($date)
    {
        if ($date instanceof \Zend_Date) {
            return $date->getTimestamp();
        }

        if ($date === true) {
            return time();
        }

        return strtotime($date);
    }

    /**
     * Retrieve current date in internal format
     *
     * @param boolean $withoutTime day only flag
     * @return string
     */
    public function now($withoutTime = false)
    {
        $format = $withoutTime ? self::DATE_PHP_FORMAT : self::DATETIME_PHP_FORMAT;
        return date($format);
    }

    /**
     * Format date to internal format
     *
     * @param string|\Zend_Date|bool|null $date
     * @param boolean $includeTime
     * @return string|null
     */
    public function formatDate($date, $includeTime = true)
    {
        if ($date === true) {
            return $this->now(!$includeTime);
        }

        if ($date instanceof \Zend_Date) {
            if ($includeTime) {
                return $date->toString(self::DATETIME_INTERNAL_FORMAT);
            } else {
                return $date->toString(self::DATE_INTERNAL_FORMAT);
            }
        }

        if (empty($date)) {
            return null;
        }

        if (!is_numeric($date)) {
            $date = $this->toTimestamp($date);
        }

        $format = $includeTime ? self::DATETIME_PHP_FORMAT : self::DATE_PHP_FORMAT;
        return date($format, $date);
    }

    /**
     * Check whether sql date is empty
     *
     * @param string $date
     * @return boolean
     */
    public function isEmptyDate($date)
    {
        return preg_replace('#[ 0:-]#', '', $date) === '';
    }
}
