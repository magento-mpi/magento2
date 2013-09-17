<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Page_Block_Html_HeadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Page_Block_Html_Head
     */
    protected $_block;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pageAssets;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_pageAssets = $this->getMock('Magento_Page_Model_Asset_GroupedCollection', array(), array(), '', false);
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento_Page_Block_Html_Head',
            array('page' => new Magento_Core_Model_Page($this->_pageAssets), 'objectManager' => $this->_objectManager)
        );
        $this->_block = $objectManagerHelper->getObject('Magento_Page_Block_Html_Head', $arguments);
    }

    protected function tearDown()
    {
        $this->_pageAssets = null;
        $this->_objectManager = null;
        $this->_block = null;
    }

    public function testAddRss()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                'link/http://127.0.0.1/test.rss',
                $this->isInstanceOf('Magento_Core_Model_Page_Asset_Remote'),
                array('attributes' => 'rel="alternate" type="application/rss+xml" title="RSS Feed"')
            );
        $assetRemoteFile = $this->getMock('Magento_Core_Model_Page_Asset_Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Magento_Core_Model_Page_Asset_Remote')
            ->will($this->returnValue($assetRemoteFile));

        $this->_block->addRss('RSS Feed', 'http://127.0.0.1/test.rss');
    }
}
