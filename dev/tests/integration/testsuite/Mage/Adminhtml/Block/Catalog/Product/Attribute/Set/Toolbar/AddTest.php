<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_AddTest extends PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        $layout = new Mage_Core_Model_Layout();

        $block = $layout->addBlock('Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Add', 'block');
        $block->setArea('adminhtml');

        $childBlock = $layout->addBlock('Mage_Core_Block_Template', 'setForm', 'block');
        $form = new Varien_Object();
        $childBlock->setForm($form);

        $expectedId = '12121212';
        $this->assertNotContains($expectedId, $block->toHtml());
        $form->setId($expectedId);
        $this->assertContains($expectedId, $block->toHtml());

        $expectedId = '665665665';
        $this->assertNotContains($expectedId, $block->toHtml());
        $form->setId($expectedId);
        $this->assertContains($expectedId, $block->toHtml());
    }
}