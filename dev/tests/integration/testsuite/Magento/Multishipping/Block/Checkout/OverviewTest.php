<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Block\Checkout;

/**
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 */
class OverviewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Multishipping\Block\Checkout\Overview
     */
    protected $_block;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_objectManager->get('Magento\Core\Model\App')
            ->loadArea(\Magento\Core\Model\App\Area::AREA_FRONTEND);
        $this->_block = $this->_objectManager->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Multishipping\Block\Checkout\Overview');
    }

    public function testGetRowItemHtml()
    {
        /** @var $item \Magento\Sales\Model\Quote\Item */
        $item = $this->_objectManager->create('Magento\Sales\Model\Quote\Item');
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product');
        $product->load(1);
        $item->setProduct($product);
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->_objectManager->create('Magento\Sales\Model\Quote');
        $item->setQuote($quote);
        // assure that default renderer was obtained
        $this->assertSelectCount('.product.name a', 1, $this->_block->getRowItemHtml($item));
    }
}
