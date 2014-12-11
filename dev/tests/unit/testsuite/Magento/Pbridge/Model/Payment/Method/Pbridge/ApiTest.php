<?php
/**
 * Unit test for \Magento\Pbridge\Model\Payment\Method\Pbridge\Api
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Model\Payment\Method\Pbridge;

class ApiTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Magento\Framework\Profiler::reset();
    }

    /**
     * @param array $data
     * @return \PHPUnit_Framework_MockObject_MockObject|Api
     */
    protected function _getApiMock(array $data)
    {
        $mock = $this->getMockBuilder(
            'Magento\Pbridge\Model\Payment\Method\Pbridge\Api'
        )->disableOriginalConstructor()->setMethods(
            ['_call']
        )->getMock();
        $mock->expects($this->once())->method('_call')->with($data);
        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getProfilerDriverMock()
    {
        return $this->getMockBuilder(
            'Magento\Framework\Profiler\DriverInterface'
        )->setMethods(
            ['start', 'stop', 'reset']
        )->getMockForAbstractClass();
    }

    /**
     * @param string $method
     * @param string $action
     * @dataProvider profilingDataProvider
     */
    public function testProfiling($method, $action)
    {
        $profilerDriver = $this->_getProfilerDriverMock();
        $profilerDriver->expects(
            $this->once()
        )->method(
            'start'
        )->with(
            'pbridge_' . $action,
            ['group' => 'pbridge', 'operation' => 'pbridge:' . $action]
        );
        $profilerDriver->expects($this->once())->method('stop')->with('pbridge_' . $action);
        \Magento\Framework\Profiler::add($profilerDriver);

        $request = new \Magento\Framework\Object();
        $request->setData('payment_action', $action);
        $api = $this->_getApiMock($request->getData());
        $api->{$method}($request);
    }

    /**
     * @return array
     */
    public function profilingDataProvider()
    {
        return [
            ['doAuthorize', 'place'],
            ['doCapture', 'capture'],
            ['doRefund', 'refund'],
            ['doVoid', 'void']
        ];
    }

    public function testValidateTokenProfiling()
    {
        $profilerDriver = $this->_getProfilerDriverMock();
        $profilerDriver->expects(
            $this->once()
        )->method(
            'start'
        )->with(
            'pbridge_validate_token',
            ['group' => 'pbridge', 'operation' => 'pbridge:validate_token']
        );
        $profilerDriver->expects($this->once())->method('stop')->with('pbridge_validate_token');
        \Magento\Framework\Profiler::add($profilerDriver);

        $api = $this->_getApiMock(['client_identifier' => 10, 'payment_action' => 'validate_token']);
        $api->validateToken(10);
    }
}
