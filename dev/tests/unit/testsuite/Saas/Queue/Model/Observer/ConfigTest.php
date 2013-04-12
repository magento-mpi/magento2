<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Queue_Model_Observer_Config
 */
class Saas_Queue_Model_Observer_ConfigTest extends PHPUnit_Framework_TestCase
{
    /*
     * @var Saas_Queue_Model_Observer_Config
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_ConfigInterface',
            array(), array(), '', false, false);
        $this->_model = new Saas_Queue_Model_Observer_Config($this->_configMock);
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_model);
    }

    public function testUseInEmailNotification()
    {
        $this->assertFalse($this->_model->useInEmailNotification());
    }

    public function testRefreshCacheWithParams()
    {
        $observer = new Varien_Event_Observer();
        $this->_configMock->expects($this->once())->method('reinit');
        $this->_model->processReinitConfig($observer);
    }
}