<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Storage helper test
 */
class Mage_Theme_Helper_StorageTest extends PHPUnit_Framework_TestCase
{
    public function testGetAllowedExtensionsByType()
    {
        /** @var $storageHelper Mage_Theme_Helper_Storage */
        $storageHelper = $this->getMock('Mage_Theme_Helper_Storage', array('getStorageType'), array(), '', false);

        $storageHelper->expects($this->at(0))
            ->method('getStorageType')
            ->will($this->returnValue('font'));

        $storageHelper->expects($this->at(1))
            ->method('getStorageType')
            ->will($this->returnValue('image'));

        $fontTypes = $storageHelper->getAllowedExtensionsByType();
        $this->assertEquals(array('ttf', 'otf', 'eot', 'svg', 'woff'), $fontTypes);

        $imagesTypes = $storageHelper->getAllowedExtensionsByType();
        $this->assertEquals(array('jpg', 'jpeg', 'gif', 'png', 'xbm', 'wbmp'), $imagesTypes);
    }

    public function testGetCurrentPath()
    {
        $currentDir = dirname(__DIR__);

        /** @var $storageHelper Mage_Theme_Helper_Storage */
        $storageHelper = $this->getMock(
            'Mage_Theme_Helper_Storage', array('getStorageRoot', '_getRequest', '_getWysiwygHelper', 'convertIdToPath'),
            array(), '', false
        );

        /** @var $request Zend_Controller_Request_Http */
        $request = $this->getMock('Zend_Controller_Request_Http', array('getParam'), array(), '', false);

        $expectedPath = __DIR__;
        $encodedExpectedPath = $storageHelper->urlEncode(__DIR__);

        $storageHelper->expects($this->once())
            ->method('getStorageRoot')
            ->will($this->returnValue($currentDir));

        $storageHelper->expects($this->once())
            ->method('_getRequest')
            ->will($this->returnValue($request));

        $storageHelper->expects($this->once())
            ->method('convertIdToPath')
            ->will($this->returnValue($expectedPath));

        $request->expects($this->once())
            ->method('getParam')
            ->will($this->returnValue($encodedExpectedPath));

        $this->assertEquals($expectedPath, $storageHelper->getCurrentPath());
    }

    /**
     * @covers Mage_Theme_Helper_Storage::getThumbnailDirectory
     */
    public function testGetThumbnailDirectory()
    {
        $imagePath = implode(DIRECTORY_SEPARATOR, array('root', 'image', 'image_name.jpg'));
        $thumbnailDir = implode(
            DIRECTORY_SEPARATOR,
            array('root', 'image', Mage_Theme_Model_Wysiwyg_Storage::THUMBNAIL_DIRECTORY)
        );

        /** @var $storageHelper Mage_Theme_Helper_Storage */
        $storageHelper = $this->getMock('Mage_Theme_Helper_Storage', null, array(), '', false);
        $this->assertEquals($thumbnailDir, $storageHelper->getThumbnailDirectory($imagePath));
    }

    /**
     * @covers Mage_Theme_Helper_Storage::getThumbnailPath
     */
    public function testGetThumbnailPath()
    {
        $image       = 'image_name.jpg';
        $storageRoot = 'root' . DIRECTORY_SEPARATOR . 'image';
        $currentPath = $storageRoot . DIRECTORY_SEPARATOR . 'some_dir';
        $imagePath   = $currentPath . DIRECTORY_SEPARATOR . $image;
        $thumbnailPath = implode(
            DIRECTORY_SEPARATOR,
            array($currentPath, Mage_Theme_Model_Wysiwyg_Storage::THUMBNAIL_DIRECTORY, $image)
        );

        $filesystem = $this->getMock('Magento_Filesystem', array('has', 'isPathInDirectory'), array(), '', false);
        $filesystem->expects($this->atLeastOnce())
            ->method('has')
            ->with($imagePath)
            ->will($this->returnValue(true));

        $filesystem::staticExpects($this->atLeastOnce())
            ->method('isPathInDirectory')
            ->with($imagePath, $storageRoot)
            ->will($this->returnValue(true));

        /** @var $storageHelper Mage_Theme_Helper_Storage */
        $storageHelper = $this->getMock(
            'Mage_Theme_Helper_Storage',
            array('getCurrentPath', 'getStorageRoot'),
            array(), '', false
        );
        $storageHelper->expects($this->once())
            ->method('getCurrentPath')
            ->will($this->returnValue($currentPath));
        $storageHelper->expects($this->atLeastOnce())
            ->method('getStorageRoot')
            ->will($this->returnValue($storageRoot));

        $filesystemProperty = new ReflectionProperty($storageHelper, '_filesystem');
        $filesystemProperty->setAccessible(true);
        $filesystemProperty->setValue($storageHelper, $filesystem);

        $this->assertEquals($thumbnailPath, $storageHelper->getThumbnailPath($image));
    }
}
