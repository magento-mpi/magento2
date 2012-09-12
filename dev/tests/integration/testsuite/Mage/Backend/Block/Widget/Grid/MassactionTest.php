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

    public static function setUpBeforeClass()
    {
        /* Point application to predefined layout fixtures */
        Mage::getConfig()->setOptions(array(
            'design_dir' => realpath( __DIR__ . '/../../_files/design'),
        ));
        Mage::getDesign()->setDesignTheme('test/default/default', 'adminhtml');

        /* Disable loading and saving layout cache */
        Mage::app()->getCacheInstance()->banUse('layout');
    }

    protected function setUp()
    {
        $this->_layout = new Mage_Core_Model_Layout(array('area' => 'adminhtml'));
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
     * @covers getItems
     * @covers getCount
     * @covers getItemsJson
     * @covers isAvailable
     */
    public function testMassactionDefaultValues()
    {
        $blockEmpty = new Mage_Backend_Block_Widget_Grid_Massaction();
        $this->assertEmpty($blockEmpty->getItems());
        $this->assertEquals(0, $blockEmpty->getCount());
        $this->assertSame('[]', $blockEmpty->getItemsJson());

        $this->assertFalse($blockEmpty->isAvailable());
    }

    /**
     * @dataProvider javascriptDataProvider
     */
    public function testJavascript($input, $expected)
    {
        if (null !== $input) {
            $this->_block->addItem($input['id'], $input);
        }
        $javascript = $this->_block->getJavaScript();
        $this->assertContains($expected, $javascript);
    }

    public function javascriptDataProvider()
    {
        return array(
            array(
                null,
                '"option_id1":{"label":"Option One","url":"*\/*\/option1","complete":"Test","id":"option_id1"}',
            ),
            array(
                null,
                '"option_id2":{"label":"Option Two","url":"*\/*\/option2","confirm":"Are you sure?","id":"option_id2"}',
            ),
            array(
                array(
                    'id' => 'option_id3',
                    'label' => 'Option Three',
                    'url' => '*/*/option3',
                    'block_name' => 'admin.test.grid.massaction.option3'
                ),
                '"option_id3":{"id":"option_id3",'
                    . '"label":"Option Three","url":"*\/*\/option3","block_name":"admin.test.grid.massaction.option3"}'
            )
        );
    }

    public function testHtml()
    {
        $html = $this->_block->toHtml();

        $this->assertRegExp('#<div id="([\w\d_]+)_massaction">#', $html);
        $this->assertRegExp('#<a href="\#" onclick="return ([\w\d_]+)_massactionJsObject.selectAll\(\)">Select All</a>#', $html);
        $this->assertRegExp('#<a href="\#" onclick="return ([\w\d_]+)_massactionJsObject.unselectAll\(\)">Unselect All</a>#', $html);
        $this->assertRegExp('#<a href="\#" onclick="return ([\w\d_]+)_massactionJsObject.selectVisible\(\)">Select Visible</a>#', $html);
        $this->assertRegExp('#<a href="\#" onclick="return ([\w\d_]+)_massactionJsObject.unselectVisible\(\)">Unselect Visible</a>#', $html);
        $this->assertRegExp('#<strong id="([\w\d_]+)_massaction-count">0</strong> items selected    </td>#', $html);
        $this->assertRegExp('#<form action="" id="([\w\d_]+)_massaction-form" method="post">#', $html);
        $this->assertRegExp('#<select id="([\w\d_]+)_massaction-select" class="required-entry select absolute-advice local-validation">#', $html);
        $this->assertRegExp('#<option value="option_id1">Option One</option>#', $html);
        $this->assertRegExp('#<option value="option_id2">Option Two</option>#', $html);
        $this->assertRegExp('#<span class="outer-span" id="([\w\d_]+)_massaction-form-hiddens"></span>#', $html);
        $this->assertRegExp('#<span class="outer-span" id="([\w\d_]+)_massaction-form-additional"></span>#', $html);
        $this->assertRegExp('#<button  id="([\w\d_]+)" title="Submit" type="button" class="scalable " onclick="([\w\d_]+)_massactionJsObject.apply\(\)" style=""><span><span><span>Submit</span></span></span></button>                        </span>#', $html);
        $this->assertRegExp('#<div id="([\w\d_]+)_massaction-item-option_id1-block">#', $html);
        $this->assertRegExp('#<div id="([\w\d_]+)_massaction-item-option_id2-block">#', $html);
    }

    public function testGridContainsMassactionColumn()
    {
        $gridBlock = $this->_layout->getBlock('admin.test.grid');
        $this->assertRegExp('#<th><span class="head-massaction"><select name="massaction" id="([\w\d_]+)_filter_massaction" class="no-changes"><option value="">Any</option><option value="1">Yes</option><option value="0">No</option></select></span></th>#', $gridBlock->toHtml());
    }

}
