<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Date_Jquery
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Converter of calendar date formats used by the jQuery datepicker.
 *
 * @category Magento
 * @package  Magento_Date_Jquery
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Magento_Date_Jquery_Calendar
{
    /**
     * Mapping that converts Zend and old calendar formats to jQuery datepicker compatible formats.
     *
     * @var array
     */
    private static $_toJavascriptDateFormatMap
        = array(
            'EEEEE' => 'D', /* Day of the week, abbreviated, three characters. */
            'EEEE'  => 'DD', /* Day of the week, complete. */
            'EEE'   => 'D',
            'ee'    => 'dd', /* Day of the week, two digits. */
            'e'     => 'd', /* Day of the week, one digit. */
            'MMMMM' => 'M', /* Month, abbreviated, three characters. */
            'MMMM'  => 'MM', /* Month, complete. */
            'MMM'   => 'M',
            'MM'    => 'mm', /* Month, two digit. */
            'M'     => 'm', /* Month, one or two digits. */
            'YYYYY' => 'yy', /* Year, up to four digits. */
            'YYYY'  => 'yy',
            'YYY'   => 'yy',
            'YY'    => 'yy',
            'Y'     => 'y', /* Year, up to two digits. */
            'yyyyy' => 'yy',
            'yyyy'  => 'yy',
            'yyy'   => 'yy',
            '%c'    => 'yy-MM-dd',
            '%A'    => 'DD',
            '%a'    => 'D',
            '%j'    => 'D',
            '%B'    => 'MM',
            '%b'    => 'M',
            '%m'    => 'mm',
            '%d'    => 'dd',
            '%e'    => 'd',
            '%Y'    => 'yy'
        );

    /**
     * Mapping that converts Zend time formats to formats compatible with the old calendar.
     *
     * @var array
     */
    private static $_toCalendarTimeFormatMap
        = array(
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
     * Convert from Zend and old calendar date formats to jQuery datepicker compatible date formats.
     * Convert from Zend time formats to old calendar time formats.
     *
     * @param string  $value
     * @param boolean $convertDate: Converts to jQuery compatible date format (e.g. %m/%d/%Y -> mm/dd/yy).
     * @param boolean $convertTime: Converts to old calendar time format (e.g. HH:mm:ss -> %H:%M:%S).
     *
     * @return string
     */
    public static function convertToDateTimeFormat($value, $convertDate = true, $convertTime = true)
    {
        if ($convertTime) {
            /* Converts from Zend time formats to old calendar time formats. */
            $value = self::_convert($value, self::$_toCalendarTimeFormatMap);
        }
        if ($convertDate) {
            /* Converts from Zend and old calendar date formats to jQuery datepicker compatible date formats. */
            $value = self::_convert($value, self::$_toJavascriptDateFormatMap);
        }
        return $value;
    }

    /**
     * Convert value by dictionary.
     *
     * @param string $value
     * @param array  $dictionary
     *
     * @return string
     */
    private static function _convert($value, $dictionary)
    {
        foreach ($dictionary as $search => $replace) {
            $value = preg_replace('/(^|[^%])' . $search . '/', '$1' . $replace, $value);
        }
        return $value;
    }
}
