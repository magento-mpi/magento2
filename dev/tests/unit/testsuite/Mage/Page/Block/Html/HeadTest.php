<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    public function testAddCss()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $assets = $this->getMock('Mage_Page_Model_GroupedAssets', array('addAsset'), array(new Mage_Core_Model_Page));
        $assets->expects($this->once())
            ->method('addAsset')
            ->with(
                Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS . '/test.css',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_ViewFile')
            );
        $assetViewFile = $this->getMock('Mage_Core_Model_Page_Asset_ViewFile', array(), array(), '', false);
        $objectManager = $this->getMock('Magento_ObjectManager');
        $objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_ViewFile')
            ->will($this->returnValue($assetViewFile));
        $block = $objectManagerHelper->getBlock(
            'Mage_Page_Block_Html_Head',
            array('assets' => $assets, 'objectManager' => $objectManager)
        );
        $block->addCss('test.css');
    }
}
