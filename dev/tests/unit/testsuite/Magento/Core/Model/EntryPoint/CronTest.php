<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\EntryPoint;

class CronTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $config = $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false);

        $this->_model = $this->getMock('\Magento\Core\Model\EntryPoint\Cron', array('_initErrorHandler'),
            array($config, $this->_objectManagerMock));
    }

    public function testProcessRequest()
    {
        $appMock = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $eventManagerMock = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $configScopeMock = $this->getMock('Magento\Config\Scope', array(), array(), '', false);

        $map = array(
            array('Magento\Core\Model\App', $appMock),
            array('Magento\Event\ManagerInterface', $eventManagerMock),
            array('Magento\Config\ScopeInterface', $configScopeMock),
            array('Magento\App\State', $appStateMock)
        );

        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValueMap($map));

        $appMock->expects($this->once())->method('setUseSessionInUrl')->with(false);
        $appMock->expects($this->once())->method('requireInstalledInstance');

        $appStateMock->expects($this->once())->method('setAreaCode')->with('crontab');
        $configScopeMock->expects($this->once())->method('setCurrentScope')->with('crontab');
        $eventManagerMock->expects($this->once())->method('dispatch')->with('default');

        $this->_model->processRequest();
    }
}
