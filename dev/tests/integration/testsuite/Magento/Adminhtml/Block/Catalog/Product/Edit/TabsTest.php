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
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento_Core_Model_Config_Scope')
            ->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        $objectManager->get('Magento_Core_Model_Registry')->register('product', $product);

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento\Core\Model\Layout');
        $layout->addBlock('Magento\Core\Block\Text', 'head');
        $layout->setArea('nonexisting'); // prevent block templates rendering
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs');
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('catalog_product_edit_tabs'));
    }
}
