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
        $objectManager->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $objectManager->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $objectManager->create('Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        $objectManager->get('Magento\Core\Model\Registry')->register('product', $product);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $objectManager->get('Magento\Core\Model\Layout');
        $layout->addBlock('Magento\Core\Block\Text', 'head');
        $layout->setArea('nonexisting'); // prevent block templates rendering
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs */
        $block = $layout->createBlock('Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs');
        $this->assertArrayHasKey(0, $block->getTabsIds());
        $this->assertNotEmpty($layout->getBlock('adminhtml\catalog\product\edit\tabs'));
    }
}
