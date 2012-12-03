<?php
/**
 * Unit test for Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Pbridge_Model_Payment_Method_Pbridge_ApiTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Magento_Profiler::reset();
    }

    /**
     * @param array $data
     * @return PHPUnit_Framework_MockObject_MockObject|Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    protected function _getApiMock(array $data)
    {
        $mock = $this->getMockBuilder('Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api')
            ->disableOriginalConstructor()
            ->setMethods(array('_call'))
            ->getMock();
        $mock->expects($this->once())
            ->method('_call')
            ->with($data);
        return $mock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getProfilerDriverMock()
    {
        return $this->getMockBuilder('Magento_Profiler_DriverInterface')
            ->setMethods(array('start', 'stop', 'reset'))
            ->getMockForAbstractClass();
    }

    /**
     * @dataProvider profilingDataProvider
     */
    public function testProfiling($method, $action)
    {
        $profilerDriver = $this->_getProfilerDriverMock();
        $profilerDriver->expects($this->once())
            ->method('start')
            ->with('pbridge_' . $action, array(
                'group' => 'pbridge',
                'operation' => 'pbridge:' . $action
            ));
        $profilerDriver->expects($this->once())
            ->method('stop')
            ->with('pbridge_' . $action);
        Magento_Profiler::add($profilerDriver);

        $request = new Varien_Object();
        $request->setData('payment_action', $action);
        $api = $this->_getApiMock($request->getData());
        $api->$method($request);
    }

    /**
     * @return array
     */
    public function profilingDataProvider()
    {
        return array(
            array('doAuthorize', 'place'),
            array('doCapture', 'capture'),
            array('doRefund', 'refund'),
            array('doVoid', 'void'),
        );
    }
}
