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
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Checkout_Block_Multishipping_Overview');
    }

    public function testGetRowItemHtml()
    {
        /** @var $item Magento_Sales_Model_Quote_Item */
        $item = Mage::getModel('Magento_Sales_Model_Quote_Item');
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);
        $item->setProduct($product);
        /** @var $quote Magento_Sales_Model_Quote */
        $quote = Mage::getModel('Magento_Sales_Model_Quote');
        $item->setQuote($quote);
        // assure that default renderer was obtained
        $this->assertSelectCount('h2.product-name a', 1, $this->_block->getRowItemHtml($item));
    }
}
