<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_From_Element_FileTest extends PHPUnit_Framework_TestCase
{
    public function testGetHtmlAttributes()
    {
        /** @var $fileBlock Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File */
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $fileBlock = $helper->getObject('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File');
        $this->assertContains('accept', $fileBlock->getHtmlAttributes());
        $this->assertContains('multiple', $fileBlock->getHtmlAttributes());
    }
}
