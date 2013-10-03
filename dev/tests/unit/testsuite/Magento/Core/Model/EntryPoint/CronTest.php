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

        $this->_model = new \Magento\Core\Model\EntryPoint\Cron($config, $this->_objectManagerMock);
    }

    public function testProcessRequest()
    {
        $appMock = $this->getMock('Magento\Core\Model\App', array(), array(), '', false);
        $eventManagerMock = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $configScopeMock = $this->getMock('Magento\Core\Model\Config\Scope', array(), array(), '', false);

        $map = array(
            array('Magento\Core\Model\App', $appMock),
            array('Magento\Core\Model\Event\Manager', $eventManagerMock),
            array('Magento\Core\Model\Config\Scope', $configScopeMock),
        );

        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValueMap($map));

        $appMock->expects($this->once())->method('setUseSessionInUrl')->with(false);
        $appMock->expects($this->once())->method('requireInstalledInstance');

        $configScopeMock->expects($this->once())->method('setCurrentScope')->with('crontab');
        $eventManagerMock->expects($this->once())->method('dispatch')->with('default');

        $this->_model->processRequest();
    }
}
