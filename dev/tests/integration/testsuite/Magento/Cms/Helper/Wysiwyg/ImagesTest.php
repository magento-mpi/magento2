<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cms_Helper_Wysiwyg_ImagesTest extends PHPUnit_Framework_TestCase
{
    public function testGetStorageRoot()
    {
        /** @var $dir \Magento\Core\Model\Dir */
        $dir = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir');
        $helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Cms\Helper\Wysiwyg\Images');
        $this->assertStringStartsWith($dir->getDir(\Magento\Core\Model\Dir::MEDIA), $helper->getStorageRoot());
    }

    public function testGetCurrentUrl()
    {
        $helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Cms\Helper\Wysiwyg\Images');
        $this->assertStringStartsWith('http://localhost/', $helper->getCurrentUrl());
    }
}
