<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for skin changing observer
 *
 * @group module:Mage_DesignEditor
 */
class Mage_DesignEditor_Model_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = new Mage_DesignEditor_Helper_Data();
    }

    public function testIsBlockDraggable()
    {
        // without layout
        $block1 = new Mage_Core_Block_Template;
        $this->assertFalse($this->_helper->isBlockDraggable($block1));

        // with layout
        $layout = new Mage_Core_Model_Layout;
        $layout->insertContainer('', 'parent');

        // block inside container
        $block2 = $layout->addBlock('Mage_Core_Block_Template', 'block2', 'parent');
        $this->assertTrue($this->_helper->isBlockDraggable($block2));

        // block is outside container
        $block3 = $layout->addBlock('Mage_Core_Block_Template', 'block3');
        $this->assertFalse($this->_helper->isBlockDraggable($block3));

        // block is inside block, which is inside container
        $block4 = $layout->addBlock('Mage_Core_Block_Template', 'block4', 'block2');
        $this->assertFalse($this->_helper->isBlockDraggable($block4));
    }
}
