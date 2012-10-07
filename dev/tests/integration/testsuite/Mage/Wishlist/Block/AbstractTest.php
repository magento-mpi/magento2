<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Wishlist_Block_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Wishlist_Block_Abstract
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = $this->getMockForAbstractClass('Mage_Wishlist_Block_Abstract');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/product_with_image.php
     */
    public function testImage()
    {
        $product = new Mage_Catalog_Model_Product();
        $product->load(1);

        $size = $this->_block->getImageSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getImageUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getImageUrl($product));
    }
}

