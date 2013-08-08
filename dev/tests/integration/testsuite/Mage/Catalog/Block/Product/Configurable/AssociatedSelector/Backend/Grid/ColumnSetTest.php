<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Block_Product_Configurable_AssociatedSelector_Backend_Grid_ColumnSetTest
    extends PHPUnit_Framework_TestCase
{

    /**
     * Testing adding column with configurable attribute to column set
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Catalog/_files/product_configurable.php
     */
    public function testPrepareSelect()
    {
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(1); // fixture
        Mage::register('current_product', $product);

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block  Mage_Catalog_Block_Product_Configurable_AssociatedSelector_Backend_Grid_ColumnSet */
        $block = $layout->createBlock(
            'Mage_Catalog_Block_Product_Configurable_AssociatedSelector_Backend_Grid_ColumnSet',
            'block'
        );
        $assertBlock = $block->getLayout()->getBlock('block.test_configurable');
        $this->assertEquals('Test Configurable', $assertBlock->getHeader());
        $this->assertEquals('test_configurable', $assertBlock->getId());
    }
}
