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

class Magento_Catalog_Block_Product_Configurable_AssociatedSelector_Backend_Grid_ColumnSetTest
    extends PHPUnit_Framework_TestCase
{

    /**
     * Testing adding column with configurable attribute to column set
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_configurable.php
     */
    public function testPrepareSelect()
    {
        $product = Mage::getModel('\Magento\Catalog\Model\Product');
        $product->load(1); // fixture
        Mage::register('current_product', $product);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getSingleton('Magento\Core\Model\Layout');
        /** @var $block  \Magento\Catalog\Block\Product\Configurable\AssociatedSelector\Backend\Grid\ColumnSet */
        $block = $layout->createBlock(
            '\Magento\Catalog\Block\Product\Configurable\AssociatedSelector\Backend\Grid\ColumnSet',
            'block'
        );
        $assertBlock = $block->getLayout()->getBlock('block.test_configurable');
        $this->assertEquals('Test Configurable', $assertBlock->getHeader());
        $this->assertEquals('test_configurable', $assertBlock->getId());
    }
}
