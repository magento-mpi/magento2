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

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetGridJsObject()
    {
        $expectedName = 'some_name';
        Mage::register('current_product', new Varien_Object);

        $layout = new Mage_Core_Model_Layout();
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config', 'block');
        $childBlock = $layout->addBlock('Mage_Core_Block_Template', 'grid', 'block');

        $this->assertNotEquals($expectedName, $block->getGridJsObject());
        $childBlock->setJsObjectName($expectedName);
        $this->assertEquals($expectedName, $block->getGridJsObject());
    }
}
