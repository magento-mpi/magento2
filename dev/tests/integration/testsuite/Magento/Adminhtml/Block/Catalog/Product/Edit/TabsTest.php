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
class Magento_Adminhtml_Block_Catalog_Product_Edit_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareLayout()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        /** @var $product Magento_Catalog_Model_Product */
        $product = $objectManager->create('Magento_Catalog_Model_Product');
        $product->load(1); // fixture
        $objectManager->get('Magento_Core_Model_Registry')->register('product', $product);

        /** @var $layout Magento_Core_Model_Layout */
        $layout =$objectManager->get('Magento_Core_Model_Layout');
        $layout->addBlock('Magento_Core_Block_Text', 'head');
        $layout->setArea('nonexisting'); // prevent block templates rendering
        /** @var $block Magento_Adminhtml_Block_Catalog_Product_Edit_Tabs */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tabs');
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('catalog_product_edit_tabs'));
    }
}
