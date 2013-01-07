<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_BlockAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * VDE toolbar buttons block
     *
     * @var Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_BlockAbstract
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = $this->getMockForAbstractClass('Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_BlockAbstract',
            array(), '', false
        );
    }

    public function testGetMode()
    {
        $this->_block->setMode(Mage_DesignEditor_Model_State::MODE_DESIGN);
        $this->assertEquals(Mage_DesignEditor_Model_State::MODE_DESIGN, $this->_block->getMode());
    }

    public function testSetMode()
    {
        $this->_block->setMode(Mage_DesignEditor_Model_State::MODE_NAVIGATION);
        $this->assertAttributeEquals(Mage_DesignEditor_Model_State::MODE_NAVIGATION, '_mode', $this->_block);
    }

    public function testIsDesignMode()
    {
        $this->_block->setMode(Mage_DesignEditor_Model_State::MODE_DESIGN);
        $this->assertTrue($this->_block->isDesignMode());

        $this->_block->setMode(Mage_DesignEditor_Model_State::MODE_NAVIGATION);
        $this->assertFalse($this->_block->isDesignMode());
    }

    public function testIsNavigationMode()
    {
        $this->_block->setMode(Mage_DesignEditor_Model_State::MODE_NAVIGATION);
        $this->assertTrue($this->_block->isNavigationMode());

        $this->_block->setMode(Mage_DesignEditor_Model_State::MODE_DESIGN);
        $this->assertFalse($this->_block->isNavigationMode());
    }
}
