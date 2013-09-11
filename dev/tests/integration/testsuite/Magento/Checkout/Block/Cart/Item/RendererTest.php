<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
 */
class Magento_Checkout_Block_Cart_Item_RendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Checkout\Block\Cart\Item\Renderer
     */
    protected $_block;

    protected function setUp()
    {
        Mage::app()->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $this->_block = Mage::app()->getLayout()->createBlock('\Magento\Checkout\Block\Cart\Item\Renderer');
        /** @var $item \Magento\Sales\Model\Quote\Item */
        $item = Mage::getModel('\Magento\Sales\Model\Quote\Item');
        $product = Mage::getModel('\Magento\Catalog\Model\Product');
        $product->load(1);
        $item->setProduct($product);
        $this->_block->setItem($item);
    }

    public function testThumbnail()
    {
        $size = $this->_block->getThumbnailSize();
        $sidebarSize = $this->_block->getThumbnailSidebarSize();
        $this->assertGreaterThan(1, $size);
        $this->assertGreaterThan(1, $sidebarSize);
        $this->assertContains('/'.$size, $this->_block->getProductThumbnailUrl());
        $this->assertContains('/'.$sidebarSize, $this->_block->getProductThumbnailSidebarUrl());
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getProductThumbnailUrl());
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getProductThumbnailSidebarUrl());
    }
}
