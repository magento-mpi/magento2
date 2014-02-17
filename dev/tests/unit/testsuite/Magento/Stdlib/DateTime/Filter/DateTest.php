<?php
/**
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Stdlib\DateTime\Filter;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $localeMock = $this->getMock('\Magento\Stdlib\DateTime\TimezoneInterface');
        $localeMock->expects($this->once())
            ->method('getDateFormat')
            ->with(\Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT)
            ->will($this->returnValue('MM-dd-yyyy'));
        $model = new Date($localeMock);
        // Check that date is converted to 'yyyy-MM-dd' format
        $this->assertEquals('2241-12-31', $model->filter('12-31-2241'));
    }
}
