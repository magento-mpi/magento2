<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Helper\Wysiwyg;

class ImagesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStorageRoot()
    {
        $path = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Filesystem')->getPath(
            \Magento\App\Filesystem::MEDIA_DIR
        );
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Cms\Helper\Wysiwyg\Images');
        $realPath = str_replace('\\', '/', $path);
        $this->assertStringStartsWith($realPath, $helper->getStorageRoot());
    }

    public function testGetCurrentUrl()
    {
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Cms\Helper\Wysiwyg\Images');
        $this->assertStringStartsWith('http://localhost/', $helper->getCurrentUrl());
    }
}
