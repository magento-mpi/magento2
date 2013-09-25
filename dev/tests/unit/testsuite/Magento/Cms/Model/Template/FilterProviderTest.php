<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cms_Model_Template_FilterProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cms_Model_Template_FilterProvider
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filterMock;

    protected function setUp()
    {
        $this->_filterMock = $this->getMock('Magento_Cms_Model_Template_Filter', array(), array(), '', false);
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_objectManagerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->_filterMock));
        $this->_model = new Magento_Cms_Model_Template_FilterProvider(
            $this->_objectManagerMock
        );
    }

    /**
     * @covers Magento_Cms_Model_Template_FilterProvider::getBlockFilter
     */
    public function testGetBlockFilter()
    {
        $this->assertInstanceOf('Magento_Cms_Model_Template_Filter', $this->_model->getBlockFilter());
    }

    /**
     * @covers Magento_Cms_Model_Template_FilterProvider::getPageFilter
     */
    public function testGetPageFilter()
    {
        $this->assertInstanceOf('Magento_Cms_Model_Template_Filter', $this->_model->getPageFilter());
    }

    /**
     * @covers Magento_Cms_Model_Template_FilterProvider::getPageFilter
     */
    public function testGetPageFilterInnerCache()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_filterMock));
        $this->_model->getPageFilter();
        $this->_model->getPageFilter();
    }

    /**
     * @covers Magento_Cms_Model_Template_FilterProvider::getPageFilter
     * @expectedException Exception
     */
    public function testGetPageWrongInstance()
    {
        $someClassMock = $this->getMock('SomeClass');
        $objectManagerMock = $this->getMock('Magento_ObjectManager');
        $objectManagerMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($someClassMock));
        $model = new Magento_Cms_Model_Template_FilterProvider(
            $objectManagerMock,
            'SomeClass',
            'SomeClass'
        );
        $model->getPageFilter();
    }
}
