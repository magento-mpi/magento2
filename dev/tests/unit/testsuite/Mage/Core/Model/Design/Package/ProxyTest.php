<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Package_ProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_View_Design_Proxy
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_packageMock;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_packageMock = $this->getMock('Mage_Core_Model_View_DesignInterface');
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Mage_Core_Model_View_Design')
            ->will($this->returnValue($this->_packageMock));
        $this->_model = new Mage_Core_Model_View_Design_Proxy($this->_objectManager);
    }

    protected function tearDown()
    {
        $this->_objectManager = null;
        $this->_model = null;
        $this->_packageMock = null;
    }

    public function testGetDesignParams()
    {
        $this->_packageMock->expects($this->once())
            ->method('getDesignParams')
            ->will($this->returnValue('return value'));
        $this->assertSame('return value', $this->_model->getDesignParams());
    }
}
