<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Stdlib\DateTime\Timezone;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Stdlib\DateTime\Timezone\Validator
     */
    protected $_validator;

    /**
     * @dataProvider validateWithTimestampOutOfSystemRangeDataProvider
     * @expectedException \Magento\Stdlib\DateTime\Timezone\ValidationException
     * @expectedExceptionMessage Transition year is out of system date range.
     */
    public function testValidateWithTimestampOutOfSystemRangeThrowsException($range, $validateArgs)
    {
        $this->_validator = new \Magento\Stdlib\DateTime\Timezone\Validator($range['min'], $range['max']);
        $this->_validator->validate($validateArgs['timestamp'], $validateArgs['to_date']);
    }

    /**
     * @expectedException \Magento\Stdlib\DateTime\Timezone\ValidationException
     * @expectedExceptionMessage Transition year is out of specified date range.
     */
    public function testValidateWithTimestampOutOfSpecifiedRangeThrowsException()
    {
        $this->_validator = new \Magento\Stdlib\DateTime\Timezone\Validator;
        $this->_validator->validate(mktime(1, 2, 3, 4, 5, 2007), mktime(1, 2, 3, 4, 5, 2006));
    }

    /**
     * @return array
     */
    public function validateWithTimestampOutOfSystemRangeDataProvider()
    {
        return array(
            array(
                array(
                    'min' => 2000,
                    'max' => 2030
                ),
                array(
                    'timestamp' => PHP_INT_MAX,
                    'to_date' => PHP_INT_MAX
                ),
            ),
            array(
                array(
                    'min' => 2000,
                    'max' => 2030
                ),
                array(
                    'timestamp' => 0,
                    'to_date' => PHP_INT_MAX
                ),
            )
        );
    }
}
