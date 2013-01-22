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
        $storageHelper = $this->getMock('Mage_Theme_Helper_Storage', array('_getStorageType'), array(), '', false);

        $storageHelper->expects($this->at(0))
            ->method('_getStorageType')
            ->will($this->returnValue('font'));

        $storageHelper->expects($this->at(1))
            ->method('_getStorageType')
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
}
