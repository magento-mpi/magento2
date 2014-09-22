<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Backend\Model\Url
 */
namespace Magento\Backend\Block\Widget;

class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @covers \Magento\Backend\Block\Widget\Grid::addRssList
     * @covers \Magento\Backend\Block\Widget\Grid::clearRss
     * @covers \Magento\Backend\Block\Widget\Grid::getRssLists
     * @dataProvider addGetClearRssDataProvider
     */
    public function testAddGetClearRss($isUseStoreInUrl)
    {
        $urlMock = $this->getMock('Magento\Framework\Url', array(), array(), '', false);

        $storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->any())->method('isUseStoreInUrl')->will($this->returnValue($isUseStoreInUrl));

        $storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', array(), array(), '', false);

        $urlBuilderMock = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);


        $urlBuilderMock->expects($this->any())->method('getUrl')->will($this->returnValue('some_url'));

        $block = $this->_objectManager->getObject(
            'Magento\Backend\Block\Widget\Grid',
            array('storeManager' => $storeManagerMock, 'urlModel' => $urlMock, 'urlBuilder' => $urlBuilderMock)
        );

        $this->assertFalse($block->getRssLists());

        $block->addRssList('some_url', 'some_label');
        $elements = $block->getRssLists();
        $element = reset($elements);
        $this->assertEquals('some_url', $element->getUrl());
        $this->assertEquals('some_label', $element->getLabel());

        $block->clearRss();
        $this->assertFalse($block->getRssLists());
    }

    /**
     * @see self::testAddGetClearRss()
     * @return array
     */
    public function addGetClearRssDataProvider()
    {
        return array(array(true), array(false));
    }
}
