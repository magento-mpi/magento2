<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 * @magentoDataFixture Mage/Backend/Block/_files/theme_registration.php
 */
class Mage_Backend_Block_Widget_Grid_MassactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_Massaction
     */
    protected $_block;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    protected function setUp()
    {
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout', array('area' => 'adminhtml'));
        $this->_layout->getUpdate()->load('layout_test_grid_handle');
        $this->_layout->generateXml();
        $this->_layout->generateElements();

        $this->_block = $this->_layout->getBlock('admin.test.grid.massaction');
    }

    protected function tearDown()
    {
        unset($this->_layout);
        unset($this->_block);
    }

    /**
     * @covers Mage_Backend_Block_Widget_Grid_Massaction::getItems
     * @covers Mage_Backend_Block_Widget_Grid_Massaction::getCount
     * @covers Mage_Backend_Block_Widget_Grid_Massaction::getItemsJson
     * @covers Mage_Backend_Block_Widget_Grid_Massaction::isAvailable
     * @magentoConfigFixture adminhtml/design/theme/full_name test/default
     */
    public function testMassactionDefaultValues()
    {
        /** @var $blockEmpty Mage_Backend_Block_Widget_Grid_Massaction */
        $blockEmpty = Mage::app()->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_Massaction');
        $this->assertEmpty($blockEmpty->getItems());
        $this->assertEquals(0, $blockEmpty->getCount());
        $this->assertSame('[]', $blockEmpty->getItemsJson());

        $this->assertFalse($blockEmpty->isAvailable());
    }

    /**
     * @magentoConfigFixture adminhtml/design/theme/full_name test/default
     */
    public function testJavascript()
    {
        $javascript = $this->_block->getJavaScript();

        $expectedItemFirst = '#"option_id1":{"label":"Option One",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/key\\\/([\w\d]+)\\\/",'
            . '"complete":"Test","id":"option_id1"}#';
        $this->assertRegExp($expectedItemFirst, $javascript);

        $expectedItemSecond = '#"option_id2":{"label":"Option Two",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/key\\\/([\w\d]+)\\\/",'
            . '"confirm":"Are you sure\?","id":"option_id2"}#';
        $this->assertRegExp($expectedItemSecond, $javascript);
    }

    /**
     * @magentoConfigFixture adminhtml/design/theme/full_name test/default
     */
    public function testJavascriptWithAddedItem()
    {
        $input = array(
            'id' => 'option_id3',
            'label' => 'Option Three',
            'url' => '*/*/option3',
            'block_name' => 'admin.test.grid.massaction.option3'
        );
        $expected = '#"option_id3":{"id":"option_id3","label":"Option Three",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/key\\\/([\w\d]+)\\\/",'
            . '"block_name":"admin.test.grid.massaction.option3"}#';

        $this->_block->addItem($input['id'], $input);
        $this->assertRegExp($expected, $this->_block->getJavaScript());
    }

    /**
     * @magentoConfigFixture adminhtml/design/theme/full_name test/default
     */
    public function testItemsCount()
    {
        $this->assertEquals(2, count($this->_block->getItems()));
        $this->assertEquals(2, $this->_block->getCount());
    }

    /**
     * @param $itemId
     * @param $expectedItem
     * @dataProvider itemsDataProvider
     * @magentoConfigFixture adminhtml/design/theme/full_name test/default
     */
    public function testItems($itemId, $expectedItem)
    {
        $items = $this->_block->getItems();
        $this->assertArrayHasKey($itemId, $items);

        $actualItem = $items[$itemId];
        $this->assertEquals($expectedItem['id'], $actualItem->getId());
        $this->assertEquals($expectedItem['label'], $actualItem->getLabel());
        $this->assertRegExp($expectedItem['url'], $actualItem->getUrl());
        $this->assertEquals($expectedItem['selected'], $actualItem->getSelected());
        $this->assertEquals($expectedItem['blockname'], $actualItem->getBlockName());
    }

    /**
     * @return array
     */
    public function itemsDataProvider()
    {
        return array(
            array(
                'option_id1',
                array(
                    'id' => 'option_id1',
                    'label' => 'Option One',
                    'url' => '#http:\/\/localhost\/index\.php\/key\/([\w\d]+)\/#',
                    'selected' => false,
                    'blockname' => ''
                )
            ),
            array(
                'option_id2',
                array(
                    'id' => 'option_id2',
                    'label' => 'Option Two',
                    'url' => '#http:\/\/localhost\/index\.php\/key\/([\w\d]+)\/#',
                    'selected' => false,
                    'blockname' => ''
                )
            )
        );
    }

    /**
     * @magentoConfigFixture adminhtml/design/theme/full_name test/default
     */
    public function testGridContainsMassactionColumn()
    {
        $this->_layout->getBlock('admin.test.grid')->toHtml();

        $gridMassactionColumn = $this->_layout->getBlock('admin.test.grid')
            ->getColumnSet()
            ->getChildBlock('massaction');
        $this->assertNotNull($gridMassactionColumn, 'Massaction column is not existed in grid column set');
        $this->assertInstanceOf(
            'Mage_Backend_Block_Widget_Grid_Column',
            $gridMassactionColumn,
            'Massaction column is not instance of Mage_Backend_Block_Widget_Column'
        );
    }
}
