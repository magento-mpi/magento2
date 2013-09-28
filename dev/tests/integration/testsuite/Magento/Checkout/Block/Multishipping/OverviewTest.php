<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Block\Multishipping;

/**
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class OverviewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Checkout\Block\Multishipping\Overview
     */
    protected $_block;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Checkout\Block\Multishipping\Overview');
    }

    public function testGetRowItemHtml()
    {
        /** @var $item \Magento\Sales\Model\Quote\Item */
        $item = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote\Item');
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load(1);
        $item->setProduct($product);
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Quote');
        $item->setQuote($quote);
        // assure that default renderer was obtained
        $this->assertSelectCount('h2.product-name a', 1, $this->_block->getRowItemHtml($item));
    }
}
