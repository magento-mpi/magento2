<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Model;

class LoggingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $qty
     * @param int|null $customerSegmentId
     * @param string $expectedText
     * @dataProvider postDispatchCustomerSegmentMatchDataProvider
     */
    public function testPostDispatchCustomerSegmentMatch($qty, $customerSegmentId, $expectedText)
    {
        $requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);
        $requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('id')
            ->will($this->returnValue($customerSegmentId));
        $resourceMock = $this->getMock('Magento\CustomerSegment\Model\Resource\Segment',
            array(), array(), '', false);
        $resourceMock->expects($this->once())
            ->method('getSegmentCustomersQty')
            ->with($customerSegmentId)
            ->will($this->returnValue($qty));

        $model = new \Magento\CustomerSegment\Model\Logging($resourceMock, $requestMock);
        $config = new \Magento\Simplexml\Element('<config/>');
        $eventMock = $this->getMock('Magento\Logging\Model\Event', array('setInfo'), array(), '', false);
        $eventMock->expects($this->once())
            ->method('setInfo')
            ->with($expectedText);

        $model->postDispatchCustomerSegmentMatch($config, $eventMock);
    }

    public function postDispatchCustomerSegmentMatchDataProvider()
    {
        return array(
            'specific segment' => array(10, 1, "Matched 10 Customers of Segment 1"),
            'no segment'       => array(10, null, '-'),
        );
    }
}
