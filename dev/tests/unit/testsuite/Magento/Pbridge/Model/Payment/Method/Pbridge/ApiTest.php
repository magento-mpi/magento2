<?php
/**
 * Unit test for \Magento\Pbridge\Model\Payment\Method\Pbridge\Api
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Pbridge_Model_Payment_Method_Pbridge_ApiTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Magento\Profiler::reset();
    }

    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
    }

    /**
     * @param array $data
     * @return PHPUnit_Framework_MockObject_MockObject|\Magento\Pbridge\Model\Payment\Method\Pbridge\Api
     */
    protected function _getApiMock(array $data)
    {
        $mock = $this->getMockBuilder('Magento\Pbridge\Model\Payment\Method\Pbridge\Api')
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
        return $this->getMockBuilder('Magento\Profiler\DriverInterface')
            ->setMethods(array('start', 'stop', 'reset'))
            ->getMockForAbstractClass();
    }

    /**
     * @param string $method
     * @param string $action
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
        \Magento\Profiler::add($profilerDriver);

        $request = new \Magento\Object();
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

    public function testValidateTokenProfiling()
    {
        $profilerDriver = $this->_getProfilerDriverMock();
        $profilerDriver->expects($this->once())
            ->method('start')
            ->with('pbridge_validate_token', array(
                'group' => 'pbridge',
                'operation' => 'pbridge:validate_token'
            ));
        $profilerDriver->expects($this->once())
            ->method('stop')
            ->with('pbridge_validate_token');
        \Magento\Profiler::add($profilerDriver);

        $api = $this->_getApiMock(array(
            'client_identifier' => 10,
            'payment_action' => 'validate_token'
        ));
        $api->validateToken(10);
    }
}
