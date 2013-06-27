<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme model
 */
class Mage_Core_Model_Theme_Image_PathTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Theme_Image_Path|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_designMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    protected function setUp()
    {
        $this->_dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_designMock = $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock('Mage_Core_Model_StoreManager', array(), array(), '', false);

        $this->_dirMock->expects($this->any())->method('getDir')->with(Mage_Core_Model_Dir::MEDIA)
            ->will($this->returnValue('/media'));

        $this->_model = new Mage_Core_Model_Theme_Image_Path(
            $this->_dirMock,
            $this->_designMock,
            $this->_storeManagerMock
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_dirMock = null;
        $this->_designMock = null;
        $this->_storeManagerMock = null;
    }

    /**
     * @covers Mage_Core_Model_Theme_Image_Path::__construct
     */
    public function testConstruct()
    {
        $this->assertNotEmpty(new Mage_Core_Model_Theme_Image_Path(
            $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Design_Package', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_StoreManager', array(), array(), '', false)
        ));
    }

    /**
     * @covers Mage_Core_Model_Theme_Image_Path::getPreviewImageDirectoryUrl
     */
    public function testPreviewImageDirectoryUrlGetter()
    {
        $store = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $store->expects($this->any())->method('getBaseUrl')->will($this->returnValue('http://localhost/'));
        $this->_storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $this->assertEquals('http://localhost/theme/preview/', $this->_model->getPreviewImageDirectoryUrl());
    }

    /**
     * @covers Mage_Core_Model_Theme_Image_Path::getPreviewImageDefaultUrl
     */
    public function testDefaultPreviewImageUrlGetter()
    {
        $this->_designMock->expects($this->once())->method('getViewFileUrl')
            ->with(Mage_Core_Model_Theme_Image_Path::DEFAULT_PREVIEW_IMAGE);
        $this->_model->getPreviewImageDefaultUrl();
    }

    /**
     * @covers Mage_Core_Model_Theme_Image_Path::getImagePreviewDirectory
     */
    public function testImagePreviewDirectoryGetter()
    {
        $this->assertEquals('/media/theme/preview', $this->_model->getImagePreviewDirectory());
    }

    /**
     * @covers Mage_Core_Model_Theme_Image_Path::getTemporaryDirectory
     */
    public function testTemporaryDirectoryGetter()
    {
        $this->assertEquals('/media/theme/origin', $this->_model->getTemporaryDirectory());
    }
}
