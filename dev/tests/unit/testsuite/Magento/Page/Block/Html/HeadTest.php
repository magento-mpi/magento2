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
     * @var \Magento\Page\Block\Html\Head
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
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_pageAssets = $this->getMock('Magento\Page\Model\Asset\GroupedCollection', array(), array(), '', false);
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento\Page\Block\Html\Head',
            array('page' => new \Magento\Core\Model\Page($this->_pageAssets), 'objectManager' => $this->_objectManager)
        );
        $this->_block = $objectManagerHelper->getObject('Magento\Page\Block\Html\Head', $arguments);
    }

    protected function tearDown()
    {
        $this->_pageAssets = null;
        $this->_objectManager = null;
        $this->_block = null;
    }

    public function testAddCss()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                \Magento\Core\Model\View\Publisher::CONTENT_TYPE_CSS . '/test.css',
                $this->isInstanceOf('Magento\Core\Model\Page\Asset\ViewFile')
            );
        $assetViewFile = $this->getMock('Magento\Core\Model\Page\Asset\ViewFile', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Magento\Core\Model\Page\Asset\ViewFile')
            ->will($this->returnValue($assetViewFile));
        $this->_block->addCss('test.css');
    }

    public function testAddJs()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                \Magento\Core\Model\View\Publisher::CONTENT_TYPE_JS . '/test.js',
                $this->isInstanceOf('Magento\Core\Model\Page\Asset\ViewFile')
            );
        $assetViewFile = $this->getMock('Magento\Core\Model\Page\Asset\ViewFile', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Magento\Core\Model\Page\Asset\ViewFile')
            ->will($this->returnValue($assetViewFile));
        $this->_block->addJs('test.js');
    }

    public function testAddRss()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                'link/http://127.0.0.1/test.rss',
                $this->isInstanceOf('Magento\Core\Model\Page\Asset\Remote'),
                array('attributes' => 'rel="alternate" type="application/rss+xml" title="RSS Feed"')
            );
        $assetRemoteFile = $this->getMock('Magento\Core\Model\Page\Asset\Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Magento\Core\Model\Page\Asset\Remote')
            ->will($this->returnValue($assetRemoteFile));

        $this->_block->addRss('RSS Feed', 'http://127.0.0.1/test.rss');
    }

    public function testAddLinkRel()
    {
        $this->_pageAssets->expects($this->once())
            ->method('add')
            ->with(
                'link/http://127.0.0.1/',
                $this->isInstanceOf('Magento\Core\Model\Page\Asset\Remote'),
                array('attributes' => 'rel="rel"')
            );
        $assetRemoteFile = $this->getMock('Magento\Core\Model\Page\Asset\Remote', array(), array(), '', false);
        $this->_objectManager->expects($this->once(''))
            ->method('create')
            ->with('Magento\Core\Model\Page\Asset\Remote')
            ->will($this->returnValue($assetRemoteFile));
        $this->_block->addLinkRel('rel', 'http://127.0.0.1/');
    }

    public function testRemoveItem()
    {
        $this->_pageAssets->expects($this->once())
            ->method('remove')
            ->with('css/test.css');
        $this->_block->removeItem('css', 'test.css');
    }

    /**
     * @dataProvider addMetaTagProvider
     */
    public function testAddMetaTag($metaTag, $content, $expected)
    {
        $this->_block->setDescription('description');
        $this->_block->setKeywords('keywords');
        $this->_block->setRobots('robots');
        $this->_block->addMetaTag($metaTag, $content);
        $this->assertEquals($expected, $this->_block->getMetaTags());
    }

    public function addMetaTagProvider()
    {
        return array(
            array(
                'metaTag' => 'test_name',
                'content' => 'test_content',
                'expected' => array(
                    array('name' => 'description', 'content' => 'description'),
                    array('name' => 'keywords', 'content' => 'keywords'),
                    array('name' => 'robots', 'content' => 'robots'),
                    array('name' => 'test_name', 'content' => 'test_content')
                )
            ),
            array(
                'metaTag' => array('name' => 'test_name', 'content' => 'test_content'),
                'content' => null,
                'expected' => array(
                    array('name' => 'description', 'content' => 'description'),
                    array('name' => 'keywords', 'content' => 'keywords'),
                    array('name' => 'robots', 'content' => 'robots'),
                    array('name' => 'test_name', 'content' => 'test_content')
                )
            ),
            array(
                'metaTag' => 'test_name',
                'content' => null,
                'expected' => array(
                    array('name' => 'description', 'content' => 'description'),
                    array('name' => 'keywords', 'content' => 'keywords'),
                    array('name' => 'robots', 'content' => 'robots')
                )
            ),
            array(
                'metaTag' => array('name' => 'test_name', 'content' => null),
                'content' => null,
                'expected' => array(
                    array('name' => 'description', 'content' => 'description'),
                    array('name' => 'keywords', 'content' => 'keywords'),
                    array('name' => 'robots', 'content' => 'robots')
                )
            ),
        );
    }

    public function testGetMetaTagHtml()
    {
        $this->_block->setDescription('description');
        $this->_block->setKeywords('keywords');
        $this->_block->setRobots('robots');
        $this->_block->addMetaTag('test_name', 'test_content');
        $expectedHtml = array(
            '<meta name="description" content="description"/>',
            '<meta name="keywords" content="keywords"/>',
            '<meta name="robots" content="robots"/>',
            '<meta name="test_name" content="test_content"/>'
        );
        $this->assertEquals(implode("\n", $expectedHtml), $this->_block->getMetaTagHtml());
    }
}
