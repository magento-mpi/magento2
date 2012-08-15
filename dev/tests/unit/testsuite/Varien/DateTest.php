<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Varien_Date test case
 */
class Varien_DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Varien_Date::convertZendToStrftime
     * @todo   Implement testConvertZendToStrftime().
     */
    public function testConvertZendToStrftime()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Varien_Date::toTimestamp
     */
    public function testToTimestamp()
    {
        $date = new Zend_Date();
        $this->assertEquals($date->getTimestamp(), Varien_Date::toTimestamp($date));

        $this->assertEquals(time(), Varien_Date::toTimestamp(true));

        $date = '2012-07-19 16:52';
        $this->assertEquals(strtotime($date), Varien_Date::toTimestamp($date));
    }

    /**
     * @covers Varien_Date::now
     */
    public function testNow()
    {
        $this->assertEquals(date(Varien_Date::DATE_PHP_FORMAT), Varien_Date::now(true));
        $this->assertEquals(date(Varien_Date::DATETIME_PHP_FORMAT), Varien_Date::now(false));
    }

    /**
     * @covers Varien_Date::formatDate
     * @todo   Implement testFormatDate().
     */
    public function testFormatDate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
