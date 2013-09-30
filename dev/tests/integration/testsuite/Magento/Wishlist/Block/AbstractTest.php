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
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_block = $this->getMockForAbstractClass('Magento_Wishlist_Block_Abstract', array(
            $objectManager->get('Magento_Core_Model_Registry'),
            $objectManager->get('Magento_Wishlist_Helper_Data'),
            $objectManager->get('Magento_Tax_Helper_Data'),
            $objectManager->get('Magento_Catalog_Helper_Data'),
            $objectManager->get('Magento_Core_Helper_Data'),
            $objectManager->get('Magento_Core_Block_Template_Context'),
            $objectManager->get('Magento_Core_Model_StoreManagerInterface'),
            $objectManager->get('Magento_Customer_Model_Session'),
            $objectManager->get('Magento_Catalog_Model_ProductFactory')
        ));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_with_image.php
     */
    public function testImage()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
            ->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_FRONTEND)
            ->setDefaultDesignTheme();
        $product = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Model_Product');
        $product->load(1);

        $size = $this->_block->getImageSize();
        $this->assertGreaterThan(1, $size);
        $this->assertContains('/'.$size, $this->_block->getImageUrl($product));
        $this->assertStringEndsWith('magento_image.jpg', $this->_block->getImageUrl($product));
    }
}
