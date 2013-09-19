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
     * @var \Magento\Checkout\Block\Multishipping\Overview
     */
    protected $_block;

    protected function setUp()
    {
        Mage::app()->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_block = Mage::app()->getLayout()->createBlock('Magento\Checkout\Block\Multishipping\Overview');
    }

    public function testGetRowItemHtml()
    {
        /** @var $item \Magento\Sales\Model\Quote\Item */
        $item = Mage::getModel('Magento\Sales\Model\Quote\Item');
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);
        $item->setProduct($product);
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = Mage::getModel('Magento\Sales\Model\Quote');
        $item->setQuote($quote);
        // assure that default renderer was obtained
        $this->assertSelectCount('h2.product-name a', 1, $this->_block->getRowItemHtml($item));
    }
}
