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

class Magento_Backend_Block_Widget_Grid_SerializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layoutMock;

    protected function setUp()
    {
        $this->_layoutMock = $this->getMock('Magento_Core_Model_Layout', array(), array(), '', false);
    }

    public function testPrepareLayout()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        $grid = $this->getMock('Magento_Adminhtml_Block_Catalog_Product_Widget_Chooser', array('getSelectedProducts'),
            array(), '', false);
        $grid->expects($this->once())->method('getSelectedProducts')->will($this->returnValue(array('product1')));
        $arguments = array(
            'data' => array(
                'grid_block' => $grid,
                'callback' => 'getSelectedProducts',
                'input_element_name' => 'selected_products_input',
                'reload_param_name' => 'selected_products_param'
            )
        );

        $block = $objectManagerHelper->getObject('Magento_Backend_Block_Widget_Grid_Serializer', $arguments);
        $block->setLayout($this->_layoutMock);

        $this->assertEquals($grid, $block->getGridBlock());
        $this->assertEquals(array('product1'), $block->getSerializeData());
        $this->assertEquals('selected_products_input', $block->getInputElementName());
        $this->assertEquals('selected_products_param', $block->getReloadParamName());
    }
}
