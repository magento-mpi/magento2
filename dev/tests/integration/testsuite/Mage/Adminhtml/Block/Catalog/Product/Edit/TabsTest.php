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

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareLayout()
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        Mage::getConfig()->setCurrentAreaCode(Mage::helper("Mage_Backend_Helper_Data")->getAreaCode());
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1); // fixture
        Mage::register('product', $product);

        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $layout->addBlock('Mage_Core_Block_Text', 'head');
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs');
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('catalog_product_edit_tabs'));
    }
}
