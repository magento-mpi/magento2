<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
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
    public function testAddGetClearRss($isUseStoreInUrl, $setStoreCount)
    {
        $urlMock = $this->getMock('Magento\Core\Model\Url', array(), array(), '', false);
        $urlMock->expects($this->at($setStoreCount))->method('setStore');
        $urlMock->expects($this->any())->method('getUrl')->will($this->returnValue('some_url'));

        $storeMock = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->any())->method('isUseStoreInUrl')->will($this->returnValue($isUseStoreInUrl));
        $storeManager = $this->getMock('Magento\Core\Model\StoreManagerInterface');
        $storeManager->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));

        /** @var $block \Magento\Backend\Block\Widget\Grid */
        $block = $this->_objectManager->getObject(
            'Magento\Backend\Block\Widget\Grid',
            array(
                'storeManager' => $storeManager,
                'urlModel' => $urlMock,
            )
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
         return array(
            array(true, 1),
            array(false, 0),
        );
    }
}
