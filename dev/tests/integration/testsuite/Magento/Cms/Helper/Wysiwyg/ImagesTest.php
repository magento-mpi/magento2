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
        /** @var $dir Magento_Core_Model_Dir */
        $dir = Mage::getObjectManager()->get('Magento_Core_Model_Dir');
        $helper = Mage::getObjectManager()->create('Magento_Cms_Helper_Wysiwyg_Images');
        $this->assertStringStartsWith($dir->getDir(Magento_Core_Model_Dir::MEDIA), $helper->getStorageRoot());
    }

    public function testGetCurrentUrl()
    {
        $helper = Mage::getObjectManager()->create('Magento_Cms_Helper_Wysiwyg_Images');
        $this->assertStringStartsWith('http://localhost/', $helper->getCurrentUrl());
    }
}
