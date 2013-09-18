<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cms_Model_Wysiwyg_Images_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Cms_Model_Wysiwyg_Images_Storage
     */
    protected $_model = null;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrlMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resizeParameters;

    protected function setUp()
    {
        $this->_filesystemMock = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_adapterFactoryMock = $this->getMock(
            'Magento_Core_Model_Image_AdapterFactory', array(), array(), '', false
        );
        $this->_viewUrlMock = $this->getMock('Magento_Core_Model_View_Url', array(), array(), '', false);
        $this->_imageHelperMock = $this->getMock('Magento_Cms_Helper_Wysiwyg_Images', array(), array(), '', false);
        $this->_resizeParameters = array('width' => 100, 'height' => 50);

        $this->_imageHelperMock->expects($this->once())
            ->method('getStorageRoot')
            ->will($this->returnValue('someDirectory'));

        $this->_filesystemMock->expects($this->once())
            ->method('setWorkingDirectory')
            ->with('someDirectory');

        $this->_filesystemMock->expects($this->once())
            ->method('setIsAllowCreateDirectories')
            ->with(true);

        $this->_model = new Magento_Cms_Model_Wysiwyg_Images_Storage(
            $this->_imageHelperMock,
            $this->getMock('Magento_Core_Helper_File_Storage_Database', array(), array(), '', false),
            $this->_filesystemMock,
            $this->_adapterFactoryMock,
            $this->_viewUrlMock,
            $this->_resizeParameters,
            array(),
            array()
        );
    }

    /**
     * @covers Magento_Cms_Model_Wysiwyg_Images_Storage::getResizeWidth
     */
    public function testGetResizeWidth()
    {
        $this->assertEquals(100, $this->_model->getResizeWidth());
    }

    /**
     * @covers Magento_Cms_Model_Wysiwyg_Images_Storage::getResizeHeight
     */
    public function testGetResizeHeight()
    {
        $this->assertEquals(50, $this->_model->getResizeHeight());
    }
}
