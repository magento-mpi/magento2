<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Cms_Helper_Wysiwyg_ImagesTest extends PHPUnit_Framework_TestCase
{
    public function testGetStorageRoot()
    {
        /** @var $dir Mage_Core_Model_Dir */
        $dir = Mage::getObjectManager()->get('Mage_Core_Model_Dir');
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $helper = new Mage_Cms_Helper_Wysiwyg_Images($filesystem);
        $this->assertStringStartsWith($dir->getDir(Mage_Core_Model_Dir::MEDIA), $helper->getStorageRoot());
    }

    public function testGetCurrentUrl()
    {
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $helper = new Mage_Cms_Helper_Wysiwyg_Images($filesystem);
        $this->assertStringStartsWith('http://localhost/', $helper->getCurrentUrl());
    }
}
