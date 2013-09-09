<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Catalog_Block_Product_New.
 *
 * @magentoDataFixture Magento/Catalog/_files/products_new.php
 */
class Magento_Catalog_Block_Product_NewTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Block_Product_New
     */
    protected $_block;

    protected function setUp()
    {
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Catalog_Block_Product_New');
        Mage::app()->getArea(Magento_Core_Model_App_Area::AREA_FRONTEND)->load();
    }

    public function testGetCacheKeyInfo()
    {
        $info = $this->_block->getCacheKeyInfo();
        $keys = array_keys($info);

        /** order and values of cache key info elements is important */

        $this->assertSame(0, array_shift($keys));
        $this->assertEquals('CATALOG_PRODUCT_NEW', $info[0]);

        $this->assertSame(1, array_shift($keys));
        $this->assertEquals(Mage::app()->getStore()->getId(), $info[1]);

        $this->assertSame(2, array_shift($keys));

        $themeModel = Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->getDesignTheme();

        $this->assertEquals($themeModel->getId() ?: null, $info[2]);

        $this->assertSame(3, array_shift($keys));
        $this->assertEquals(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId(), $info[3]);

        $this->assertSame('template', array_shift($keys));

        /**
         * This block is implemented without template by default (invalid).
         * Having the cache key fragment with empty value can potentially lead to caching bugs
         */
        $this->assertSame(4, array_shift($keys));
        $this->assertNotEquals('', $info[4]);
    }

    public function testSetGetProductsCount()
    {
        $this->assertEquals(Magento_Catalog_Block_Product_New::DEFAULT_PRODUCTS_COUNT,
            $this->_block->getProductsCount());
        $this->_block->setProductsCount(100);
        $this->assertEquals(100, $this->_block->getProductsCount());
    }

    public function testToHtml()
    {
        $this->assertEmpty($this->_block->getProductCollection());

        $this->_block->setProductsCount(5);
        $this->_block->setTemplate('product/widget/new/content/new_list.phtml');
        $this->_block->setLayout(Mage::getSingleton('Magento_Core_Model_Layout'));

        $html = $this->_block->toHtml();
        $this->assertNotEmpty($html);
        $this->assertContains('New Product', $html);
        $this->assertInstanceOf(
            'Magento_Catalog_Model_Resource_Product_Collection', $this->_block->getProductCollection()
        );
    }
}
