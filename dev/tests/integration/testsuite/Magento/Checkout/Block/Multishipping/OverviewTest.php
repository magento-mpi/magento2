<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class Magento_Checkout_Block_Multishipping_OverviewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Checkout_Block_Multishipping_Overview
     */
    protected $_block;

    protected function setUp()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
            ->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $this->_block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Checkout_Block_Multishipping_Overview');
    }

    public function testGetRowItemHtml()
    {
        /** @var $item Magento_Sales_Model_Quote_Item */
        $item = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Quote_Item');
        /** @var $product Magento_Catalog_Model_Product */
        $product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
        $product->load(1);
        $item->setProduct($product);
        /** @var $quote Magento_Sales_Model_Quote */
        $quote = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Quote');
        $item->setQuote($quote);
        // assure that default renderer was obtained
        $this->assertSelectCount('h2.product-name a', 1, $this->_block->getRowItemHtml($item));
    }
}
