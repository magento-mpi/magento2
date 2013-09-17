<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Block_Product_List_ToolbarTest extends PHPUnit_Framework_TestCase
{
    public function testGetPagerHtml()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Catalog_Block_Product_List_Toolbar */
        $block = $layout->createBlock('Magento_Catalog_Block_Product_List_Toolbar', 'block');
        /** @var $childBlock Magento_Core_Block_Text */
        $childBlock = $layout->addBlock('Magento_Core_Block_Text', 'product_list_toolbar_pager', 'block');

        $expectedHtml = '<b>Any text there</b>';
        $this->assertNotEquals($expectedHtml, $block->getPagerHtml());
        $childBlock->setText($expectedHtml);
        $this->assertEquals($expectedHtml, $block->getPagerHtml());
    }
}
