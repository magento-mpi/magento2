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
     * @param string $expected
     * @param string $actual
     *
     * @dataProvider convertToDateTimeFormatDataProvider
     */
    public function testConvertToDateTimeFormat($expected, $actual)
    {
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function convertToDateTimeFormatDataProvider()
    {
        return array(
            array("mm/dd/yy", Magento_Date_Jquery_Calendar::convertToDateTimeFormat("%m/%d/%Y", true, false)),
            array("%H:%M:%S", Magento_Date_Jquery_Calendar::convertToDateTimeFormat("HH:mm:ss", false, true)),
            array("mm/dd/yy %H:%M:%S",
                  Magento_Date_Jquery_Calendar::convertToDateTimeFormat("%m/%d/%Y HH:mm:ss", true, true)),
            array("%m/%d/%Y HH:mm:ss",
                  Magento_Date_Jquery_Calendar::convertToDateTimeFormat("%m/%d/%Y HH:mm:ss", false, false))
        );
    }
}
