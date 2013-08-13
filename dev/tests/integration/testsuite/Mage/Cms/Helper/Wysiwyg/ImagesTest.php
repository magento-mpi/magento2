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
        $dir = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Mage_Core_Model_Dir');
        $helper = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Cms_Helper_Wysiwyg_Images');
        $this->assertStringStartsWith($dir->getDir(Mage_Core_Model_Dir::MEDIA), $helper->getStorageRoot());
    }

    public function testGetCurrentUrl()
    {
        $helper = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Cms_Helper_Wysiwyg_Images');
        $this->assertStringStartsWith('http://localhost/', $helper->getCurrentUrl());
    }
}
