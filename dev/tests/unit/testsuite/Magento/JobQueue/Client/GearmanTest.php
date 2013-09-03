<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_JobQueue_Client_GearmanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\JobQueue\Client\Gearman
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adaptedClientMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento\JobQueue\Client\ConfigInterface');
        $this->_configMock->expects($this->once())->method('getServers')->will($this->returnValue('127.0.0.1:4730'));
        $this->_adaptedClientMock = $this->getMock(
            'GearmanClient',
            array('addServers', 'doBackground', 'doHighBackground', 'doLowBackground'),
            array(), '', false
        );
        $this->_adaptedClientMock->expects($this->once())->method('addServers')->with('127.0.0.1:4730');
        $this->_model = new \Magento\JobQueue\Client\Gearman($this->_configMock, $this->_adaptedClientMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_configMock);
        unset($this->_adaptedClientMock);
    }

    /**
     * @dataProvider addBackgroundTaskDataProvider
     */
    public function testAddBackgroundTask()
    {
        $data = array('123s', 12);
        $preparedData = '["123s",12]';

        $this->_adaptedClientMock->expects($this->once())->method('doBackground')->with('some_event', $preparedData);
        $this->_model->addBackgroundTask('some_event', $data);
    }

    public function addBackgroundTaskDataProvider()
    {
        return array(
            array('low',  'doLowBackground'),
            array('middle',  'doBackground'),
            array('high',  'doHighBackground'),
        );
    }
}
