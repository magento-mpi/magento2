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

namespace Magento\Catalog\Block\Product\Configurable\AssociatedSelector\Backend\Grid;

class ColumnSetTest
    extends \PHPUnit_Framework_TestCase
{

    /**
     * Testing adding column with configurable attribute to column set
     *
     * @magentoAppArea adminhtml
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     */
    public function testPrepareSelect()
    {
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Registry')->register('current_product', $product);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $block  \Magento\Catalog\Block\Product\Configurable\AssociatedSelector\Backend\Grid\ColumnSet */
        $block = $layout->createBlock(
            'Magento\Catalog\Block\Product\Configurable\AssociatedSelector\Backend\Grid\ColumnSet',
            'block'
        );
        $assertBlock = $block->getLayout()->getBlock('block.test_configurable');
        $this->assertEquals('Test Configurable', $assertBlock->getHeader());
        $this->assertEquals('test_configurable', $assertBlock->getId());
    }
}
