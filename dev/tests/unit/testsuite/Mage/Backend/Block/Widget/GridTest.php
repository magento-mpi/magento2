<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Model_Url
 */
class Mage_Backend_Block_Widget_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new Magento_Test_Helper_ObjectManager($this);
    }

    /**
     * @covers Mage_Backend_Block_Widget_Grid::addRssList
     * @covers Mage_Backend_Block_Widget_Grid::clearRss
     * @covers Mage_Backend_Block_Widget_Grid::getRssLists
     * @dataProvider addGetClearRssDataProvider
     */
    public function testAddGetClearRss($isUseStoreInUrl, $setStoreCount)
    {
        $urlMock = $this->getMock('Mage_Core_Model_Url', array(), array(), '', false);
        $urlMock->expects($this->at($setStoreCount))->method('setStore');
        $urlMock->expects($this->any())->method('getUrl')->will($this->returnValue('some_url'));

        $storeMock = $this->getMock('Mage_Core_Model_Store', array('isUseStoreInUrl'), array(), '', false);
        $storeMock->expects($this->any())->method('isUseStoreInUrl')->will($this->returnValue($isUseStoreInUrl));
        $storeManager = $this->getMock('Mage_Core_Model_StoreManagerInterface');
        $storeManager->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));

        /** @var $block Mage_Backend_Block_Widget_Grid */
        $block = $this->_objectManager->getObject(
            'Mage_Backend_Block_Widget_Grid',
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
