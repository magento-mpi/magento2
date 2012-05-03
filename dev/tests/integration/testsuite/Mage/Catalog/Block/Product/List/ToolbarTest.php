<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Block_Product_List_ToolbarTest extends PHPUnit_Framework_TestCase
{
    public function testGetPagerHtml()
    {
        $layout = new Mage_Core_Model_Layout;
        $block = $layout->createBlock('Mage_Catalog_Block_Product_List_Toolbar', 'block');
        $childBlock = $layout->addBlock('Mage_Core_Block_Text', 'product_list_toolbar_pager', 'block');

        $expectedHtml = '<b>Any text there</b>';
        $this->assertNotEquals($expectedHtml, $block->getPagerHtml());
        $childBlock->setText($expectedHtml);
        $this->assertEquals($expectedHtml, $block->getPagerHtml());
    }
}
