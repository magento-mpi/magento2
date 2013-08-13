<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Test class Saas_Launcher_Block_Adminhtml_Storelauncher_Product_TileTest
 *
 * @magentoAppArea adminhtml
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Product_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture limitations/catalog_product 1
     */
    public function testIsAddProductRestricted()
    {
        /** @var Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile $block */
        $block = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile');
        $this->assertFalse($block->isAddProductRestricted());
    }

    public function testIsAddProductRestrictedNoLimit()
    {
        /** @var Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile $block */
        $block = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile');
        $this->assertFalse($block->isAddProductRestricted());
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @magentoConfigFixture limitations/catalog_product 1
     */
    public function testIsAddProductRestrictedLimitReached()
    {
        /** @var Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile $block */
        $block = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile');
        $this->assertTrue($block->isAddProductRestricted());
    }
}
