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

class Magento_Wishlist_Block_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Wishlist_Block_Abstract
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = $this->getMockForAbstractClass('Magento_Wishlist_Block_Abstract', array(
            Mage::getModel('Magento_Wishlist_Helper_Data'),
            Mage::getModel('Magento_Tax_Helper_Data'),
            Mage::getModel('Magento_Catalog_Helper_Data'),
            Mage::getModel('Magento_Core_Helper_Data'),
            Mage::getModel('Magento_Core_Block_Template_Context'),
        ));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testImage()
    {
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->setDefaultDesignTheme();

        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);

        $size = $this->_block->getImageSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getImageUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getImageUrl($product));
    }
}
