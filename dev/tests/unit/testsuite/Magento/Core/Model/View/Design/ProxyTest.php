<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\View\Design;

class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\View\Design\Proxy
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\View\DesignInterface
     */
    protected $_viewDesign;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_viewDesign = $this->getMock('Magento\Core\Model\View\DesignInterface');
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Magento\Core\Model\View\Design')
            ->will($this->returnValue($this->_viewDesign));
        $this->_model = new \Magento\Core\Model\View\Design\Proxy($this->_objectManager);
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
