<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/Backend/Block/_files/backend_theme.php
 *
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Block_Widget_Grid_MassactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_Widget_Grid_Massaction
     */
    protected $_block;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-6406');

        parent::setUp();

        $this->_setFixtureTheme();

        $this->_layout = Mage::getModel('Magento_Core_Model_Layout', array('area' => 'adminhtml'));
        $this->_layout->getUpdate()->load('layout_test_grid_handle');
        $this->_layout->generateXml();
        $this->_layout->generateElements();

        $this->_block = $this->_layout->getBlock('admin.test.grid.massaction');
    }

    /**
     * Set fixture theme for admin backend area
     */
    protected function _setFixtureTheme()
    {
        Magento_TestFramework_Helper_Bootstrap::getInstance()->reinitialize(array(
            Magento_Core_Model_App::PARAM_RUN_CODE => 'admin',
            Magento_Core_Model_App::PARAM_RUN_TYPE => 'store',
            Magento_Core_Model_App::PARAM_APP_DIRS => array(
                Magento_Core_Model_Dir::THEMES => __DIR__ . '/../../_files/design'
            ),
        ));
    }

    /**
     * @covers Magento_Backend_Block_Widget_Grid_Massaction::getItems
     * @covers Magento_Backend_Block_Widget_Grid_Massaction::getCount
     * @covers Magento_Backend_Block_Widget_Grid_Massaction::getItemsJson
     * @covers Magento_Backend_Block_Widget_Grid_Massaction::isAvailable
     */
    public function testMassactionDefaultValues()
    {
        /** @var $blockEmpty Magento_Backend_Block_Widget_Grid_Massaction */
        $blockEmpty = Mage::app()->getLayout()->createBlock('Magento_Backend_Block_Widget_Grid_Massaction');
        $this->assertEmpty($blockEmpty->getItems());
        $this->assertEquals(0, $blockEmpty->getCount());
        $this->assertSame('[]', $blockEmpty->getItemsJson());

        $this->assertFalse($blockEmpty->isAvailable());
    }

    public function testGetJavaScript()
    {
        $javascript = $this->_block->getJavaScript();

        $expectedItemFirst = '#"option_id1":{"label":"Option One",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/(?:key\\\/([\w\d]+)\\\/)?",'
            . '"complete":"Test","id":"option_id1"}#';
        $this->assertRegExp($expectedItemFirst, $javascript);

        $expectedItemSecond = '#"option_id2":{"label":"Option Two",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/(?:key\\\/([\w\d]+)\\\/)?",'
            . '"confirm":"Are you sure\?","id":"option_id2"}#';
        $this->assertRegExp($expectedItemSecond, $javascript);
    }

    public function testGetJavaScriptWithAddedItem()
    {
        $input = array(
            'id' => 'option_id3',
            'label' => 'Option Three',
            'url' => '*/*/option3',
            'block_name' => 'admin.test.grid.massaction.option3'
        );
        $expected = '#"option_id3":{"id":"option_id3","label":"Option Three",'
            . '"url":"http:\\\/\\\/localhost\\\/index\.php\\\/(?:key\\\/([\w\d]+)\\\/)?",'
            . '"block_name":"admin.test.grid.massaction.option3"}#';

        $this->_block->addItem($input['id'], $input);
        $this->assertRegExp($expected, $this->_block->getJavaScript());
    }

    public function testGetCount()
    {
        $this->assertEquals(2, $this->_block->getCount());
    }

    /**
     * @param $itemId
     * @param $expectedItem
     * @dataProvider getItemsDataProvider
     */
    public function testGetItems($itemId, $expectedItem)
    {
        $items = $this->_block->getItems();
        $this->assertCount(2, $items);
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
    public function getItemsDataProvider()
    {
        return array(
            array(
                'option_id1',
                array(
                    'id' => 'option_id1',
                    'label' => 'Option One',
                    'url' => '#http:\/\/localhost\/index\.php\/(?:key\/([\w\d]+)\/)?#',
                    'selected' => false,
                    'blockname' => ''
                )
            ),
            array(
                'option_id2',
                array(
                    'id' => 'option_id2',
                    'label' => 'Option Two',
                    'url' => '#http:\/\/localhost\/index\.php\/(?:key\/([\w\d]+)\/)?#',
                    'selected' => false,
                    'blockname' => ''
                )
            )
        );
    }

    public function testGridContainsMassactionColumn()
    {
        $this->_layout->getBlock('admin.test.grid')->toHtml();

        $gridMassactionColumn = $this->_layout->getBlock('admin.test.grid')
            ->getColumnSet()
            ->getChildBlock('massaction');
        $this->assertNotNull($gridMassactionColumn, 'Massaction column does not exist in the grid column set');
        $this->assertInstanceOf(
            'Magento_Backend_Block_Widget_Grid_Column',
            $gridMassactionColumn,
            'Massaction column is not an instance of Magento_Backend_Block_Widget_Column'
        );
    }
}
