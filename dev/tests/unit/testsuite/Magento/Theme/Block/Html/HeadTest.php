<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html;

class HeadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Block\Html\Head
     */
    protected $_block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pageAssets;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /** @var  \Magento\View\Element\Template\Context */
    protected $_context;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_pageAssets = $this->getMock('Magento\View\Asset\GroupedCollection', array(), array(), '', false);
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento\Theme\Block\Html\Head',
            array('assets' => $this->_pageAssets, 'objectManager' => $this->_objectManager)
        );
        $this->_context = $arguments['context'];
        $this->_block = $objectManagerHelper->getObject('Magento\Theme\Block\Html\Head', $arguments);
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
                $this->isInstanceOf('Magento\View\Asset\Remote'),
                array('attributes' => 'rel="alternate" type="application/rss+xml" title="RSS Feed"')
            );
        $assetRemoteFile = $this->getMock('Magento\View\Asset\Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Magento\View\Asset\Remote')
            ->will($this->returnValue($assetRemoteFile));

        $this->_block->addRss('RSS Feed', 'http://127.0.0.1/test.rss');
    }

    public function testGetFaviconFile()
    {
        $storeMock = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('baseUrl/'));
        $this->_context->getStoreManager()
            ->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($storeMock));

        $this->_context->getStoreConfig()
            ->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue('storeConfig'));

        $mediaDirMock = $this->getMock('\Magento\Filesystem\Directory\Read', array(), array(), '', false);
        $mediaDirMock->expects($this->any())
            ->method('isFile')
            ->with('favicon/storeConfig')
            ->will($this->returnValue(true));
        $this->_context->getFilesystem()
            ->expects($this->once())
            ->method('getDirectoryRead')
            ->will($this->returnValue($mediaDirMock));

        $this->assertEquals('baseUrl/favicon/storeConfig', $this->_block->getFaviconFile());
    }
}
