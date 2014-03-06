<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * \Magento\Stdlib\DateTime test case
 */
namespace Magento\Stdlib;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $_dateTime;

    protected function setUp()
    {
        $this->_dateTime = new \Magento\Stdlib\DateTime;
    }

    public function testToTimestamp()
    {
        $date = new \Magento\Stdlib\DateTime\Date();
        $dateTime = new \Magento\Stdlib\DateTime;
        $this->assertEquals($date->getTimestamp(), $dateTime->toTimestamp($date));

        $this->assertEquals(time(), $dateTime->toTimestamp(true));

        $date = '2012-07-19 16:52';
        $this->assertEquals(strtotime($date), $dateTime->toTimestamp($date));
    }

    public function testNow()
    {
        $this->assertEquals(date(\Magento\Stdlib\DateTime::DATE_PHP_FORMAT), $this->_dateTime->now(true));
        $this->assertEquals(date(\Magento\Stdlib\DateTime::DATETIME_PHP_FORMAT), $this->_dateTime->now(false));
    }

    /**
     * @dataProvider formatDateDataProvider
     *
     * expectedFormat is to be in the Y-m-d type format for the date you are expecting,
     * expectedResult is if a specific date is expected.
     */
    public function testFormatDate($date, $includeTime, $expectedFormat, $expectedResult = null)
    {
        $dateTime = new \Magento\Stdlib\DateTime;
        $actual = $dateTime->formatDate($date, $includeTime);
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
        // Take care when calling date here as it can be called much earlier than when testFormatDate
        // executes thus causing a discrepancy in the actual vs expected time. See MAGETWO-10296
        $date = new \Magento\Stdlib\DateTime\Date();
        return array(
            'null' => array(null, false, ''),
            'null including Time' => array(null, true, ''),
            'Bool true' => array(true, false, 'Y-m-d'),
            'Bool true including Time' => array(true, true, 'Y-m-d H:i:s'),
            'Bool false' => array(false, false, ''),
            'Bool false including Time' => array(false, true, ''),
            'Zend Date' => array($date, false, date('Y-m-d', $date->getTimestamp())),
            'Zend Date including Time' => array($date, true, date('Y-m-d H:i:s', $date->getTimestamp())),
        );
    }

    /**
     * @param string $date
     * @param bool $expected
     *
     * @dataProvider isEmptyDateDataProvider
     */
    public function testIsEmptyDate($date, $expected)
    {
        $actual = $this->_dateTime->isEmptyDate($date);
        $this->assertEquals($actual, $expected);
    }

    /**
     * @return array
     */
    public function isEmptyDateDataProvider()
    {
        return array(
            array('', true),
            array(' ', true),
            array('0000-00-00', true),
            array('0000-00-00 00:00:00', true),
            array('2000-10-10', false),
            array('2000-10-10 10:10:10', false),
        );
    }
}
