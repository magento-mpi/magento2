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
        $this->markTestSkipped('MAGETWO-18846');
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        $objectManager->get('Magento\View\DesignInterface')
            ->setDefaultDesignTheme();
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $objectManager->create('Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        $objectManager->get('Magento\Core\Model\Registry')->register('product', $product);

        $objectManager->get('Magento\App\State')->setAreaCode('nonexisting');
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $objectManager->get('Magento\View\LayoutInterface');
        $layout->addBlock('Magento\View\Element\Text', 'head');
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs */
        $block = $layout->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs');
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('adminhtml\product\edit\tabs'));
    }
}
