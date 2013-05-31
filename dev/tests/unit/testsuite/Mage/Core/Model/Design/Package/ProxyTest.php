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
     * @var Mage_Core_Model_Design_Package_Proxy
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
        $this->_packageMock = $this->getMock('Mage_Core_Model_Design_PackageInterface');
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Mage_Core_Model_Design_Package')
            ->will($this->returnValue($this->_packageMock));
        $this->_model = new Mage_Core_Model_Design_Package_Proxy($this->_objectManager);
    }

    protected function tearDown()
    {
        $this->_objectManager = null;
        $this->_model = null;
        $this->_packageMock = null;
    }

    public function testGetPublicFileUrl()
    {
        $this->_packageMock->expects($this->once())
            ->method('getPublicFileUrl')
            ->with('file', true)
            ->will($this->returnValue('return value'));
        $this->assertSame('return value', $this->_model->getPublicFileUrl('file', true));
    }

    public function testGetPublicDir()
    {
        $this->_packageMock->expects($this->once())
            ->method('getPublicDir')
            ->will($this->returnValue('return value'));
        $this->assertSame('return value', $this->_model->getPublicDir());
    }

    public function testGetViewFilePublicPath()
    {
        $this->_packageMock->expects($this->once())
            ->method('getViewFilePublicPath')
            ->with('file.css', array(1, 2))
            ->will($this->returnValue('return value'));
        $this->assertSame('return value', $this->_model->getViewFilePublicPath('file.css', array(1, 2)));
    }
}
