<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerSegment_Model_LoggingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $qty
     * @param int|null $customerSegmentId
     * @param string $expectedText
     * @dataProvider postDispatchCustomerSegmentMatchDataProvider
     */
    public function testPostDispatchCustomerSegmentMatch($qty, $customerSegmentId, $expectedText)
    {
        $requestMock = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('id')
            ->will($this->returnValue($customerSegmentId));
        $resourceMock = $this->getMock('Magento_CustomerSegment_Model_Resource_Segment',
            array(), array(), '', false);
        $resourceMock->expects($this->once())
            ->method('getSegmentCustomersQty')
            ->with($customerSegmentId)
            ->will($this->returnValue($qty));

        $model = new Magento_CustomerSegment_Model_Logging($resourceMock, $requestMock);
        $config = new Magento_Simplexml_Element('<config/>');
        $eventMock = $this->getMock('Magento_Logging_Model_Event', array('setInfo'), array(), '', false);
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
