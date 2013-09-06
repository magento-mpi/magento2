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
     * @var Magento_Checkout_Block_Cart_Item_Renderer
     */
    protected $_block;

    protected function setUp()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Checkout_Block_Cart_Item_Renderer');
        /** @var $item Magento_Sales_Model_Quote_Item */
        $item = Mage::getModel('Magento_Sales_Model_Quote_Item');
        $product = Mage::getModel('Magento_Catalog_Model_Product');
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
