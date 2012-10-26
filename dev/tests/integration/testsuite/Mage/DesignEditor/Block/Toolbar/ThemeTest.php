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
 * Test for theme block functioning
 */
class Mage_DesignEditor_Block_Toolbar_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Block_Toolbar_Theme
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = new Mage_DesignEditor_Block_Toolbar_Theme();
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testIsThemeSelected()
    {
        $oldTheme = Mage::getDesign()->getDesignTheme();
        Mage::getDesign()->setDesignTheme('a/b');
        $isSelected = $this->_block->isThemeSelected('a/b');
        Mage::getDesign()->setDesignTheme($oldTheme);
        $this->assertTrue($isSelected);

        Mage::getDesign()->setDesignTheme('a/b');
        $isSelected = $this->_block->isThemeSelected('c/b');
        Mage::getDesign()->setDesignTheme($oldTheme);
        $this->assertFalse($isSelected);
    }

    public function testGetSelectHtmlId()
    {
        $value = $this->_block->getSelectHtmlId();
        $this->assertNotEmpty($value);
    }
}
