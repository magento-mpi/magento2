<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ProductAlert_Block_Email_StockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ProductAlert\Block\Email\Stock
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Magento\ProductAlert\Block\Email\Stock');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testThumbnail()
    {
        Mage::app()->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);

        $size = $this->_block->getThumbnailSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getThumbnailUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getThumbnailUrl($product));
    }
}
