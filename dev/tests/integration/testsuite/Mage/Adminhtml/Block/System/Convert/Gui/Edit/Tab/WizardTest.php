<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard.
 *
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_WizardTest extends PHPUnit_Framework_TestCase
{
    public function testGetShortDateFormat()
    {
        $block = new Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard;
        $this->assertNotEmpty($block->getShortDateFormat());
    }
}
