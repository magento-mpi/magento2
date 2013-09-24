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
        $coreRegistry = $objectManager->get('Magento\Core\Model\Registry');
        $wishlistData = $objectManager->get('Magento\Wishlist\Helper\Data');
        $taxData = $objectManager->get('Magento\Tax\Helper\Data');
        $catalogData = $objectManager->get('Magento\Catalog\Helper\Data');
        $coreData = $objectManager->get('Magento\Core\Helper\Data');
        $context = $objectManager->get('Magento\Core\Block\Template\Context');

        $this->_block = $this->getMockForAbstractClass('Magento\Wishlist\Block\AbstractBlock', array(
            $coreRegistry, $wishlistData, $taxData, $catalogData, $coreData, $context
        ));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testImage()
    {
        \Mage::app()->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)->load();
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_FRONTEND)
            ->setDefaultDesignTheme();
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);

        $size = $this->_block->getImageSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getImageUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getImageUrl($product));
    }
}
