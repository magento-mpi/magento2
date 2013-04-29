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
     * @var Magento_JobQueue_Client_Gearman
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
        $this->_configMock = $this->getMock('Magento_JobQueue_Client_ConfigInterface');
        $this->_adaptedClientMock = $this->getMock('GearmanClient', array(), array(), '', false);
        $this->_model = new Magento_JobQueue_Client_Gearman($this->_configMock, $this->_adaptedClientMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_configMock);
        unset($this->_adaptedClientMock);
    }
}
