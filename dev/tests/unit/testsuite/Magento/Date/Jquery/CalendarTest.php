<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Date_Jquery
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Date_Jquery_CalendarTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test conversions from old calendar date/time formats to jQuery datepicker compatible formats.
     *
     * @param string  $expected
     * @param string  $dateFormat - Date format to convert
     * @param boolean $formatDate - Whether to convert date (true) or not (false)
     * @param boolean $formatTime - Whether to convert time (true) or not (false)
     *
     * @dataProvider convertToDateTimeFormatDataProvider
     */
    public function testConvertToDateTimeFormat($expected, $formatString, $formatDate, $formatTime)
    {
        $this->assertEquals(
            $expected, Magento_Date_Jquery_Calendar::convertToDateTimeFormat($formatString, $formatDate, $formatTime)
        );
    }

    /**
     * @return array
     */
    public function convertToDateTimeFormatDataProvider()
    {
        return array(
            array("mm/dd/yy", "%m/%d/%Y", true, false),
            array("%H:%M:%S", "HH:mm:ss", false, true),
            array("mm/dd/yy %H:%M:%S", "%m/%d/%Y HH:mm:ss", true, true),
            array("%m/%d/%Y HH:mm:ss", "%m/%d/%Y HH:mm:ss", false, false)
        );
    }
}
