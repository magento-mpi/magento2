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
    public function testToTimestamp()
    {
        $date = new Zend_Date();
        $this->assertEquals($date->getTimestamp(), Varien_Date::toTimestamp($date));

        $this->assertEquals(time(), Varien_Date::toTimestamp(true));

        $date = '2012-07-19 16:52';
        $this->assertEquals(strtotime($date), Varien_Date::toTimestamp($date));
    }

    public function testNow()
    {
        $this->assertEquals(date(Varien_Date::DATE_PHP_FORMAT), Varien_Date::now(true));
        $this->assertEquals(date(Varien_Date::DATETIME_PHP_FORMAT), Varien_Date::now(false));
    }

    /**
     * @dataProvider formatDateDataProvider
     */
    public function testFormatDate($date, $includeTime, $expectedResult)
    {
        $actual = Varien_Date::formatDate($date, $includeTime);
        $this->assertEquals($expectedResult, $actual);
    }

    /**
     * @return array
     */
    public function formatDateDataProvider()
    {
        $date = new Zend_Date();
        return array(
            'null' => array(null, false, ''),
            'Bool true' => array(true, false, date('Y-m-d')),
            'Bool false' => array(false, false, ''),
            'Zend Date' => array($date, false, date('Y-m-d', $date->getTimestamp())),
            'Zend Date including Time' => array($date, true, date('Y-m-d H:i:s', $date->getTimestamp())),
        );
    }
}
