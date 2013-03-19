<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Page_Block_Html_Head
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assets;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_assets = $this->getMock(
            'Mage_Page_Model_GroupedAssets', array('addAsset', 'removeAsset'), array(new Mage_Core_Model_Page)
        );
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Mage_Page_Block_Html_Head',
            array('assets' => $this->_assets, 'objectManager' => $this->_objectManager)
        );
        $this->_block = $objectManagerHelper->getObject('Mage_Page_Block_Html_Head', $arguments);
    }

    protected function tearDown()
    {
        $this->_assets = null;
        $this->_objectManager = null;
        $this->_block = null;
    }

    public function testAddCss()
    {
        $this->_assets->expects($this->once())
            ->method('addAsset')
            ->with(
                Mage_Core_Model_Design_Package::CONTENT_TYPE_CSS . '/test.css',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_ViewFile')
            );
        $assetViewFile = $this->getMock('Mage_Core_Model_Page_Asset_ViewFile', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_ViewFile')
            ->will($this->returnValue($assetViewFile));
        $this->_block->addCss('test.css');
    }

    public function testAddJs()
    {
        $this->_assets->expects($this->once())
            ->method('addAsset')
            ->with(
                Mage_Core_Model_Design_Package::CONTENT_TYPE_JS . '/test.js',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_ViewFile')
            );
        $assetViewFile = $this->getMock('Mage_Core_Model_Page_Asset_ViewFile', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_ViewFile')
            ->will($this->returnValue($assetViewFile));
        $this->_block->addJs('test.js');
    }

    public function testAddRss()
    {
        $this->_assets->expects($this->once())
            ->method('addAsset')
            ->with(
                'link/http://127.0.0.1/test.rss',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_Remote'),
                array('attributes' => 'rel="alternate" type="application/rss+xml" title="RSS Feed"')
            );
        $assetRemoteFile = $this->getMock('Mage_Core_Model_Page_Asset_Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_Remote')
            ->will($this->returnValue($assetRemoteFile));

        $this->_block->addRss('RSS Feed', 'http://127.0.0.1/test.rss');
    }

    public function testAddLinkRel()
    {
        $this->_assets->expects($this->once())
            ->method('addAsset')
            ->with(
                'link/http://127.0.0.1/',
                $this->isInstanceOf('Mage_Core_Model_Page_Asset_Remote'),
                array('attributes' => 'rel="rel"')
            );
        $assetRemoteFile = $this->getMock('Mage_Core_Model_Page_Asset_Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Mage_Core_Model_Page_Asset_Remote')
            ->will($this->returnValue($assetRemoteFile));
        $this->_block->addLinkRel('rel', 'http://127.0.0.1/');
    }

    public function testRemoveItem()
    {
        $this->_assets->expects($this->once())
            ->method('removeAsset')
            ->with('css/test.css');
        $this->_block->removeItem('css', 'test.css');
    }
}
