<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_TestFramework_Annotation_ConfigFixture.
 */
class Magento_Test_Annotation_ConfigFixtureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Annotation_ConfigFixture|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = $this->getMock(
            'Magento_TestFramework_Annotation_ConfigFixture',
            array('_getConfigValue', '_setConfigValue')
        );
    }

    /**
     * @magentoConfigFixture web/unsecure/base_url http://example.com/
     */
    public function testGlobalConfig()
    {
        $this->_object
            ->expects($this->at(0))
            ->method('_getConfigValue')
            ->with('web/unsecure/base_url')
            ->will($this->returnValue('http://localhost/'))
        ;
        $this->_object
            ->expects($this->at(1))
            ->method('_setConfigValue')
            ->with('web/unsecure/base_url', 'http://example.com/')
        ;
        $this->_object->startTest($this);

        $this->_object
            ->expects($this->once())
            ->method('_setConfigValue')
            ->with('web/unsecure/base_url', 'http://localhost/')
        ;
        $this->_object->endTest($this);
    }

    /**
     * @magentoConfigFixture current_store dev/restrict/allow_ips 192.168.0.1
     */
    public function testCurrentStoreConfig()
    {
        $this->_object
            ->expects($this->at(0))
            ->method('_getConfigValue')
            ->with('dev/restrict/allow_ips', '')
            ->will($this->returnValue('127.0.0.1'))
        ;
        $this->_object
            ->expects($this->at(1))
            ->method('_setConfigValue')
            ->with('dev/restrict/allow_ips', '192.168.0.1', '')
        ;
        $this->_object->startTest($this);

        $this->_object
            ->expects($this->once())
            ->method('_setConfigValue')
            ->with('dev/restrict/allow_ips', '127.0.0.1', '')
        ;
        $this->_object->endTest($this);
    }

    /**
     * @magentoConfigFixture admin_store dev/restrict/allow_ips 192.168.0.2
     */
    public function testSpecificStoreConfig()
    {
        $this->_object
            ->expects($this->at(0))
            ->method('_getConfigValue')
            ->with('dev/restrict/allow_ips', 'admin')
            ->will($this->returnValue('192.168.0.1'))
        ;
        $this->_object
            ->expects($this->at(1))
            ->method('_setConfigValue')
            ->with('dev/restrict/allow_ips', '192.168.0.2', 'admin')
        ;
        $this->_object->startTest($this);

        $this->_object
            ->expects($this->once())
            ->method('_setConfigValue')
            ->with('dev/restrict/allow_ips', '192.168.0.1', 'admin')
        ;
        $this->_object->endTest($this);
    }

    /**
     * @magentoConfigFixture some/config/path some_config_value
     */
    public function testInitStoreAfterOfScope()
    {
        $this->_object
            ->expects($this->never())
            ->method('_getConfigValue')
        ;
        $this->_object
            ->expects($this->never())
            ->method('_setConfigValue')
        ;
        $this->_object->initStoreAfter();
    }

    /**
     * @magentoConfigFixture web/unsecure/base_url http://example.com/
     */
    public function testInitStoreAfter()
    {
        $this->_object->startTest($this);
        $this->_object
            ->expects($this->at(0))
            ->method('_getConfigValue')
            ->with('web/unsecure/base_url')
            ->will($this->returnValue('http://localhost/'))
        ;
        $this->_object
            ->expects($this->at(1))
            ->method('_setConfigValue')
            ->with('web/unsecure/base_url', 'http://example.com/')
        ;
        $this->_object->initStoreAfter();
    }
}
