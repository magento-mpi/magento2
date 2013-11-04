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

namespace Magento\Catalog\Block\Adminhtml\Product\Edit;

/**
 * @magentoAppArea adminhtml
 */
class TabsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareLayout()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\View\DesignInterface')
            ->setArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento\Config\ScopeInterface')
            ->setCurrentScope(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $objectManager->create('Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        $objectManager->get('Magento\Core\Model\Registry')->register('product', $product);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $objectManager->get('Magento\View\LayoutInterface');
        $layout->addBlock('Magento\Core\Block\Text', 'head');
        $layout->setArea('nonexisting'); // prevent block templates rendering
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs */
        $block = $layout->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs');
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('adminhtml\product\edit\tabs'));
    }
}
