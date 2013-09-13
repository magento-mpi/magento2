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
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        Mage::register('product', $product);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getModel('Magento\Core\Model\Layout');
        $layout->addBlock('Magento\Core\Block\Text', 'head');
        $layout->setArea('nonexisting'); // prevent block templates rendering
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs */
        $block = $layout->createBlock('Magento\Adminhtml\Block\Catalog\Product\Edit\Tabs');
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('catalog_product_edit_tabs'));
    }
}
