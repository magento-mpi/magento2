<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Date
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Converter of date formats
 * Internal dates
 *
 * @category Varien
 * @package  Varien_Date
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Varien_Jquery_Date extends Varien_Date
{
    /**
     * Zend Date To Jquery datepicker compatible date according Map array
     *
     * @var array
     */
    private static $_convertZendToJavascriptDate = array(
        'EEEEE' => 'D',  /* Day of the week, abbreviated, three characters. */
        'EEEE'  => 'DD', /* Day of the week, complete. */
        'EEE'   => 'D',
        'ee'    => 'dd', /* Day of the week, two digits. */
        'e'     => 'd',  /* Day of the week, one digit. */
        'MMMMM' => 'M',  /* Month, abbreviated, three characters. */
        'MMMM'  => 'MM', /* Month, complete. */
        'MMM'   => 'M',
        'MM'    => 'mm', /* Month, two digit. */
        'M'     => 'm',  /* Month, one or two digits. */
        'YYYYY' => 'yy', /* Year, up to four digits. */
        'YYYY'  => 'yy',
        'YYY'   => 'yy',
        'YY'    => 'yy',
        'Y'     => 'y',  /* Year, up to two digits. */
        'yyyyy' => 'yy',
        'yyyy'  => 'yy',
        'yyy'   => 'yy',
    );

    /**
     * Old calendar date to Zend Date according Map array
     *
     * @var array
     */
    private static $_convertStrftimeDateToZend = array(
        '%c' => 'yy-MM-dd',
        '%A' => 'EEEE',
        '%a' => 'EEE',
        '%j' => 'D',
        '%B' => 'MMMM',
        '%b' => 'MMM',
        '%m' => 'MM',
        '%d' => 'dd',
        '%e' => 'd',
        '%Y' => 'yy'
    );

    /**
     * Zend Date To local time according Map array
     *
     * @var array
     */
    private static $_convertZendToStrftimeTime = array(
        'a'  => '%p',
        'hh' => '%I',
        'h'  => '%I',
        'HH' => '%H',
        'H'  => '%H',
        'mm' => '%M',
        'ss' => '%S',
        'z'  => '%Z',
        'v'  => '%Z'
    );

    /**
     * Convert Zend Date format to local time/date according format
     *
     * @param string $value
     * @param boolean $convertDate
     * @param boolean $convertTime
     * @return string
     */
    public static function convertZendToStrftime($value, $convertDate = true, $convertTime = true)
    {
        if ($convertTime) {
            $value = self::_convert($value, self::$_convertZendToStrftimeTime);
        }
        if ($convertDate) {
            /* Convert from old calendar format to Zend first, then to Javascript compatible format. */
            $value = self::_convert($value, self::$_convertStrftimeDateToZend);
            $value = self::_convert($value, self::$_convertZendToJavascriptDate);
        }
        return $value;
    }

    /**
     * Convert value by dictionary
     *
     * @param string $value
     * @param array $dictionary
     * @return string
     */
    protected static function _convert($value, $dictionary)
    {
        foreach ($dictionary as $search => $replace) {
            $value = preg_replace('/(^|[^%])' . $search . '/', '$1' . $replace, $value);
        }
        return $value;
    }
}
