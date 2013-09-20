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
        /** @var $dir \Magento\Core\Model\Dir */
        $dir = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir');
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Cms\Helper\Wysiwyg\Images');
        $this->assertStringStartsWith($dir->getDir(\Magento\Core\Model\Dir::MEDIA), $helper->getStorageRoot());
    }

    public function testGetCurrentUrl()
    {
        $helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Cms\Helper\Wysiwyg\Images');
        $this->assertStringStartsWith('http://localhost/', $helper->getCurrentUrl());
    }
}
