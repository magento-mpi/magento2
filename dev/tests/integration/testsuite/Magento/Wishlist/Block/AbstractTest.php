<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Block;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Wishlist\Block\AbstractBlock
     */
    protected $_block;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_block = $this->getMockForAbstractClass(
            'Magento\Wishlist\Block\AbstractBlock', 
            array(
                $objectManager->get('Magento\Catalog\Block\Product\Context'),
                $objectManager->get('Magento\Framework\App\Http\Context'),
                $objectManager->get('Magento\Catalog\Model\ProductFactory'),
            )
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testImage()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea(\Magento\Framework\App\Area::AREA_FRONTEND);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\View\DesignInterface')
            ->setDefaultDesignTheme();
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load(1);

        $size = $this->_block->getImageSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/' . $size, $this->_block->getImageUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getImageUrl($product));
    }
}
