<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Theme_ImageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test get preview image
     */
    public function testGetPreviewImageUrl()
    {
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeModel->getThemeImage()->setPreviewImage('preview_image.jpg');
        $this->assertEquals('http://localhost/pub/media/theme/preview/preview_image.jpg',
            $themeModel->getThemeImage()->getPreviewImageUrl());

        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeImageModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Image');
        $themeImageModel->setTheme($themeModel);
        $themeImageModel->setPreviewImage('preview_image.jpg');
        $this->assertEquals('http://localhost/pub/media/theme/preview/preview_image.jpg',
            $themeImageModel->getPreviewImageUrl());
    }

    /**
     * Test get preview image default
     */
    public function testGetPreviewImageDefaultUrl()
    {
        $defPreviewImageUrl = 'default_image_preview_url';
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeImageModel = $this->getMock('Mage_Core_Model_Theme_Image', array('_getPreviewImageDefaultUrl'),
            array(), '', false);
        $themeImageModel->setTheme($themeModel);
        $themeImageModel->expects($this->once())
            ->method('_getPreviewImageDefaultUrl')
            ->will($this->returnValue($defPreviewImageUrl));

        $this->assertEquals($defPreviewImageUrl, $themeImageModel->getPreviewImageUrl());
    }
}