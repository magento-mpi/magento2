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
     * magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareLayout()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block + fixture');

        Mage::getConfig()->setCurrentAreaCode(Mage::helper("Mage_Backend_Helper_Data")->getAreaCode());
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1); // fixture
        Mage::register('product', $product);

        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $layout->addBlock('Mage_Core_Block_Text', 'head');
        $block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs');
        $layout->addBlock($block);
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('catalog_product_edit_tabs'));
    }
}
