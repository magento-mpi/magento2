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
        /** @var $dir \Magento\App\Dir */
        $dir = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Dir');
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Cms\Helper\Wysiwyg\Images');
        $this->assertStringStartsWith($dir->getDir(\Magento\App\Dir::MEDIA), $helper->getStorageRoot());
    }

    public function testGetCurrentUrl()
    {
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Cms\Helper\Wysiwyg\Images');
        $this->assertStringStartsWith('http://localhost/', $helper->getCurrentUrl());
    }
}
