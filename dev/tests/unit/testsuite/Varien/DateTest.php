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
     *
     * expectedFormat is to be in the Y-m-d type format for the date you are expecting,
     * expectedResult is if a specific date is expected.
     */
    public function testFormatDate($date, $includeTime, $expectedFormat, $expectedResult = null)
    {
        $actual = Varien_Date::formatDate($date, $includeTime);
        if ($expectedFormat != '') {
            $expectedResult = date($expectedFormat);
        } else {
            if ($expectedResult === null) {
                $expectedResult = '';
            }
        }
        $this->assertEquals($expectedResult, $actual);
    }

    /**
     * @return array
     */
    public function formatDateDataProvider()
    {
        // Do not call date here as it can be called much earlier than when testFormatDate executes thus causing a
        // discrepancy in the actual vs expected time. See MAGETWO-10296
        return array(
            'null' => array(null, false, ''),
            'null' => array(null, true, ''),
            'Bool true' => array(true, false, 'Y-m-d'),
            'Bool true' => array(true, true, 'Y-m-d H:i:s'),
            'Bool false' => array(false, false, ''),
            'Bool false' => array(false, true, ''),
            // Date is called here since Zend_Date is getting the time at this point. To ensure they match it
            // needs to be called here.
            'Zend Date' => array(new Zend_Date(), false, '', date('Y-m-d')),
            'Zend Date including Time' => array(
                new Zend_Date(),
                true,
                '',
                date('Y-m-d H:i:s')),
        );
    }
}
