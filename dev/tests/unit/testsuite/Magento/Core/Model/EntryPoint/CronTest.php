<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_EntryPoint_CronTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $config = $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false);

        $this->_model = $this->getMock(
            'Magento_Core_Model_EntryPoint_Cron',
            array('_setGlobalObjectManager'),
            array($config, $this->_objectManagerMock)
        );
    }

    public function testProcessRequest()
    {
        $appMock = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $eventManagerMock = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);

        $map = array(
            array('Magento_Core_Model_App', $appMock),
            array('Magento_Core_Model_Event_Manager', $eventManagerMock),
        );

        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValueMap($map));

        $appMock->expects($this->once())->method('setUseSessionInUrl')->with(false);
        $appMock->expects($this->once())->method('requireInstalledInstance');

        $eventManagerMock->expects($this->once())->method('addEventArea')->with('crontab');
        $eventManagerMock->expects($this->once())->method('dispatch')->with('default');

        $this->_model->processRequest();
    }
}
