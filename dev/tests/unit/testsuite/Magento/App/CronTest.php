<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class CronTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Cron
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    protected function setUp()
    {
        $this->_eventManagerMock = $this->getMock('Magento\Event\ManagerInterface');
        $this->_stateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_request = $this->getMock('Magento\App\Console\Request', array(), array(), '', false);
        $this->_responseMock = $this->getMock('Magento\App\Console\Response', array(), array(), '', false);
        $this->_model = new Cron(
            $this->_eventManagerMock,
            $this->_stateMock,
            $this->_request,
            $this->_responseMock
        );
    }

    public function testLaunchDispatchesCronEvent()
    {
        $this->_stateMock->expects($this->once())->method('setAreaCode')->with('crontab');
        $this->_eventManagerMock->expects($this->once())->method('dispatch')->with('default');
        $this->_responseMock->expects($this->once())->method('setCode')->with(0);
        $this->assertEquals($this->_responseMock, $this->_model->launch());
    }
}
