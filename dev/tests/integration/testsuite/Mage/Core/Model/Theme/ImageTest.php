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
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Image');
        $themeModel->getThemeImage()->setPreviewImage('preview_image.jpg');
        $this->assertEquals('http://localhost/pub/media/theme/preview/preview_image.jpg',
            $themeModel->getThemeImage()->getPreviewImageUrl());

        $themeImageModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Image');
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
        $themeModel = $this->getMock(
            'Mage_Core_Model_Theme_Image', array('_getPreviewImageDefaultUrl'), array(), '', false
        );
        $themeModel->expects($this->once())
            ->method('_getPreviewImageDefaultUrl')
            ->will($this->returnValue($defPreviewImageUrl));

        $this->assertEquals($defPreviewImageUrl, $themeModel->getPreviewImageUrl());
    }
}
