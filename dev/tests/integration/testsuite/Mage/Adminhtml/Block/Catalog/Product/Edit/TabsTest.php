<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareLayout()
    {
        $product = new Mage_Catalog_Model_Product;
        $product->load(1); // fixture
        Mage::register('product', $product);

        $layout = new Mage_Core_Model_Layout;
        $layout->addBlock('Mage_Core_Block_Text', 'head');
        $block = new Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs;
        $layout->addBlock($block);
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('adminhtml.catalog.product.edit.tab.attributes'));
    }
}
