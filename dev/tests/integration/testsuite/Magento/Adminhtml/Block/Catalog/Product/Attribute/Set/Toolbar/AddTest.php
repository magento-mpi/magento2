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
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_AddTest extends PHPUnit_Framework_TestCase
{
    public function testToHtmlFormId()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');

        $block = $layout->addBlock('Magento_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Add', 'block');
        $block->setArea('adminhtml')->unsetChild('setForm');

        $childBlock = $layout->addBlock('Magento_Core_Block_Template', 'setForm', 'block');
        $form = new Magento_Object();
        $childBlock->setForm($form);

        $expectedId = '12121212';
        $this->assertNotContains($expectedId, $block->toHtml());
        $form->setId($expectedId);
        $this->assertContains($expectedId, $block->toHtml());
    }
}
