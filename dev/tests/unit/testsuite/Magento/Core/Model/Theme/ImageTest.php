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
 * Test theme image model
 */
class Magento_Core_Model_Theme_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Theme_Image
     */
    protected $_model;

    /**
     * @var Magento_Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var Magento_Image|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageMock;

    /**
     * @var Magento_Core_Model_Theme_Image_Uploader|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_uploaderMock;

    /**
     * @var Magento_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeMock;

    protected function setUp()
    {
        $this->_filesystemMock = $this->getMock('Magento_Filesystem', array(), array(), '', false, false);
        $imageFactory = $this->getMock('Magento_Core_Model_Image_Factory', array(), array(), '', false, false);
        $this->_imageMock = $this->getMock('Magento_Image', array(), array(), '', false, false);
        $imageFactory->expects($this->any())->method('create')->will($this->returnValue($this->_imageMock));

        $this->_themeMock = $this->getMock('Magento_Core_Model_Theme', null, array(), '', false, false);
        $this->_uploaderMock = $this->getMock('Magento_Core_Model_Theme_Image_Uploader',
            array(), array(), 'Magento_Core_Model_Theme_Image_UploaderProxy', false, false);
        $logger = $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false, false);

        $this->_model = new Magento_Core_Model_Theme_Image(
            $this->_filesystemMock,
            $imageFactory,
            $this->_uploaderMock,
            $this->_getImagePathMock(),
            $logger,
            $this->_themeMock
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_filesystemMock = null;
        $this->_imageMock = null;
        $this->_uploaderMock = null;
        $this->_themeMock = null;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Theme_Image_Path
     */
    protected function _getImagePathMock()
    {
        $imagePathMock = $this->getMock('Magento_Core_Model_Theme_Image_Path', array(), array(), '', false);
        $testBaseUrl = 'http://localhost/media_path/';
        $imagePathMock->expects($this->any())->method('getPreviewImageDirectoryUrl')
            ->will($this->returnValue($testBaseUrl));
        $imagePathMock->expects($this->any())->method('getPreviewImageDefaultUrl')
            ->will($this->returnValue($testBaseUrl . 'test_default_preview.png'));

        $testBaseDir = '/media_path/';
        $imagePathMock->expects($this->any())->method('getImagePreviewDirectory')
            ->will($this->returnValue($testBaseDir . 'theme/preview'));
        $imagePathMock->expects($this->any())->method('getTemporaryDirectory')
            ->will($this->returnValue($testBaseDir . 'tmp'));
        return $imagePathMock;
    }

    /**
     * Sample theme data
     *
     * @return array
     */
    protected function _getThemeSampleData()
    {
        return array(
            'theme_id'             => 1,
            'theme_title'          => 'Sample theme',
            'preview_image'        => 'images/preview.png',
            'area'                 => Magento_Core_Model_App_Area::AREA_FRONTEND,
            'type'                 => Magento_Core_Model_Theme::TYPE_VIRTUAL
        );
    }

    /**
     * @covers Magento_Core_Model_Theme_Image::__construct
     */
    public function testConstructor()
    {
        $themeImage = new Magento_Core_Model_Theme_Image(
            $this->_filesystemMock,
            $this->getMock('Magento_Core_Model_Image_Factory', array(), array(), '', false, false),
            $this->_uploaderMock,
            $this->_getImagePathMock(),
            $this->getMock('Magento_Core_Model_Logger', array(), array(), '', false, false),
            $this->_themeMock
        );
        $this->assertNotEmpty($themeImage);
    }

    /**
     * @covers Magento_Core_Model_Theme_Image::createPreviewImage
     */
    public function testCreatePreviewImage()
    {
        $this->_themeMock->setData($this->_getThemeSampleData());
        $this->_themeMock->setData('preview_image', null);
        $this->_imageMock->expects($this->once())->method('save')->with('/media_path/theme/preview', $this->anything());
        $this->_model->createPreviewImage('/some/path/to/image.png');
        $this->assertNotNull($this->_themeMock->getData('preview_image'));
    }

    /**
     * @covers Magento_Core_Model_Theme_Image::createPreviewImageCopy
     */
    public function testCreatePreviewImageCopy()
    {
        $previewImage = 'test_preview.png';
        $this->_themeMock->setData($this->_getThemeSampleData());
        $this->_themeMock->setData('preview_image', $previewImage);

        $this->_filesystemMock->expects($this->any())->method('copy')
            ->with('/media_path/theme/preview', $this->anything());
        $this->assertFalse($this->_model->createPreviewImageCopy($previewImage));
        $this->assertEquals($previewImage, $this->_themeMock->getData('preview_image'));
    }

    /**
     * @covers Magento_Core_Model_Theme_Image::removePreviewImage
     */
    public function testRemovePreviewImage()
    {
        $this->_themeMock->setData($this->_getThemeSampleData());
        $this->_themeMock->setData('preview_image', 'test.png');
        $this->_filesystemMock->expects($this->once())->method('delete');
        $this->_model->removePreviewImage();
        $this->assertNull($this->_themeMock->getData('preview_image'));
    }

    /**
     * @covers Magento_Core_Model_Theme_Image::removePreviewImage
     */
    public function testRemoveEmptyPreviewImage()
    {
        $this->_themeMock->setData($this->_getThemeSampleData());
        $this->_themeMock->setData('preview_image', null);
        $this->_filesystemMock->expects($this->never())->method('delete');
        $this->_model->removePreviewImage();
        $this->assertNull($this->_themeMock->getData('preview_image'));
    }

    /**
     * @covers Magento_Core_Model_Theme_Image::uploadPreviewImage
     */
    public function testUploadPreviewImage()
    {
        $scope = 'test_scope';
        $tmpFilePath = '/media_path/tmp/temporary.png';
        $this->_themeMock->setData($this->_getThemeSampleData());
        $this->_themeMock->setData('preview_image', 'test.png');
        $this->_uploaderMock->expects($this->once())->method('uploadPreviewImage')->with($scope, '/media_path/tmp')
            ->will($this->returnValue($tmpFilePath));
        $this->_filesystemMock->expects($this->at(0))->method('delete')->with($this->stringContains('test.png'));
        $this->_filesystemMock->expects($this->at(1))->method('delete')->with($tmpFilePath);
        $this->_model->uploadPreviewImage($scope);
    }

    /**
     * @covers Magento_Core_Model_Theme_Image::getPreviewImageUrl
     */
    public function testGetPreviewImageUrl()
    {
        $this->_themeMock->setData($this->_getThemeSampleData());
        $this->_themeMock->setData('preview_image', 'preview/image.png');
        $this->assertEquals('http://localhost/media_path/preview/image.png', $this->_model->getPreviewImageUrl());
    }

    /**
     * @covers Magento_Core_Model_Theme_Image::getPreviewImageUrl
     */
    public function testGetDefaultPreviewImageUrl()
    {
        $this->_themeMock->setData($this->_getThemeSampleData());
        $this->_themeMock->setData('preview_image', null);
        $this->assertEquals('http://localhost/media_path/test_default_preview.png',
            $this->_model->getPreviewImageUrl());
    }
}
