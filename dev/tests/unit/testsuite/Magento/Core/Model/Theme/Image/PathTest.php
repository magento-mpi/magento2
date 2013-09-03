<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test of image path model
 */
class Magento_Core_Model_Theme_Image_PathTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Theme_Image_Path|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewUrlMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    protected function setUp()
    {
        $this->_dirMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $this->_viewUrlMock = $this->getMock('Magento_Core_Model_View_Url', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock('Magento_Core_Model_StoreManager', array(), array(), '', false);

        $this->_dirMock->expects($this->any())->method('getDir')->with(Magento_Core_Model_Dir::MEDIA)
            ->will($this->returnValue('/media'));

        $this->_model = new Magento_Core_Model_Theme_Image_Path(
            $this->_dirMock,
            $this->_viewUrlMock,
            $this->_storeManagerMock
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_dirMock = null;
        $this->_viewUrlMock = null;
        $this->_storeManagerMock = null;
    }

    /**
     * @covers Magento_Core_Model_Theme_Image_Path::__construct
     * @covers Magento_Core_Model_Theme_Image_Path::getPreviewImageDirectoryUrl
     */
    public function testPreviewImageDirectoryUrlGetter()
    {
        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())->method('getBaseUrl')->will($this->returnValue('http://localhost/'));
        $this->_storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $this->assertEquals('http://localhost/theme/preview/', $this->_model->getPreviewImageDirectoryUrl());
    }

    /**
     * @covers Magento_Core_Model_Theme_Image_Path::getPreviewImageDefaultUrl
     */
    public function testDefaultPreviewImageUrlGetter()
    {
        $this->_viewUrlMock->expects($this->once())->method('getViewFileUrl')
            ->with(Magento_Core_Model_Theme_Image_Path::DEFAULT_PREVIEW_IMAGE);
        $this->_model->getPreviewImageDefaultUrl();
    }

    /**
     * @covers Magento_Core_Model_Theme_Image_Path::getImagePreviewDirectory
     */
    public function testImagePreviewDirectoryGetter()
    {
        $expectedPath = implode(
            Magento_Filesystem::DIRECTORY_SEPARATOR,
            array('/media', 'theme', 'preview')
        );
        $this->assertEquals($expectedPath, $this->_model->getImagePreviewDirectory());
    }

    /**
     * @covers Magento_Core_Model_Theme_Image_Path::getTemporaryDirectory
     */
    public function testTemporaryDirectoryGetter()
    {
        $expectedPath = implode(
            Magento_Filesystem::DIRECTORY_SEPARATOR,
            array('/media', 'theme', 'origin')
        );
        $this->assertEquals($expectedPath, $this->_model->getTemporaryDirectory());
    }
}
