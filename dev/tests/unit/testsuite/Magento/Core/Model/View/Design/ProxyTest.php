<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_View_Design_ProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_View_Design_Proxy
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_View_DesignInterface
     */
    protected $_viewDesign;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_viewDesign = $this->getMock('Magento_Core_Model_View_DesignInterface');
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Magento_Core_Model_View_Design')
            ->will($this->returnValue($this->_viewDesign));
        $this->_model = new Magento_Core_Model_View_Design_Proxy($this->_objectManager);
    }

    protected function tearDown()
    {
        $this->_objectManager = null;
        $this->_model = null;
        $this->_viewDesign = null;
    }

    public function testGetDesignParams()
    {
        $this->_viewDesign->expects($this->once())
            ->method('getDesignParams')
            ->will($this->returnValue('return value'));
        $this->assertSame('return value', $this->_model->getDesignParams());
    }
}
