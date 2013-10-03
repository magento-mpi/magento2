<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
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
        $this->_block = $this->getMockForAbstractClass('Magento\Wishlist\Block\AbstractBlock', array(
            $objectManager->get('Magento\Core\Model\Registry'),
            $objectManager->get('Magento\Wishlist\Helper\Data'),
            $objectManager->get('Magento\Tax\Helper\Data'),
            $objectManager->get('Magento\Catalog\Helper\Data'),
            $objectManager->get('Magento\Core\Helper\Data'),
            $objectManager->get('Magento\Core\Block\Template\Context'),
            $objectManager->get('Magento\Core\Model\StoreManagerInterface'),
            $objectManager->get('Magento\Customer\Model\Session'),
            $objectManager->get('Magento\Catalog\Model\ProductFactory')
        ));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testImage()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)
            ->setDefaultDesignTheme();
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Product');
        $product->load(1);

        $size = $this->_block->getImageSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getImageUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getImageUrl($product));
    }
}
