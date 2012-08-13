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
     * Test conversions from old calendar date formats to jQuery datepicker compatible formats.
     * Test Zend time formats to internal time formats.
     */
    public function testConvert()
    {
        $this->assertEquals("mm/dd/yy", Magento_Date_Jquery_Calendar::convertZendToStrftime("%m/%d/%Y", true, false));
        $this->assertEquals("%H:%M:%S", Magento_Date_Jquery_Calendar::convertZendToStrftime("HH:mm:ss", false, true));
        $this->assertEquals(
            "mm/dd/yy %H:%M:%S", Magento_Date_Jquery_Calendar::convertZendToStrftime("%m/%d/%Y HH:mm:ss", true, true)
        );
        $this->assertEquals(
            "%m/%d/%Y HH:mm:ss", Magento_Date_Jquery_Calendar::convertZendToStrftime("%m/%d/%Y HH:mm:ss", false, false)
        );
        $this->assertEquals(
            Magento_Date_Jquery_Calendar::convertZendToStrftime("%m/%d/%Y HH:mm:ss"),
            Magento_Date_Jquery_Calendar::convertToDateTimeFormat("%m/%d/%Y HH:mm:ss")
        );
    }
}
