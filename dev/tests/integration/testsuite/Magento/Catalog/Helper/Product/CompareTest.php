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

class Magento_Catalog_Helper_Product_CompareTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Helper_Product_Compare
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Magento_Catalog_Helper_Product_Compare');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testGetListUrl()
    {
        /** @var $empty Magento_Catalog_Helper_Product_Compare */
        $empty = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Catalog_Helper_Product_Compare');
        $this->assertContains('/catalog/product_compare/index/', $empty->getListUrl());

        $this->_populateCompareList();
        $this->assertRegExp('#/catalog/product_compare/index/items/(?:10,11|11,10)/#', $this->_helper->getListUrl());
    }

    public function testGetAddUrl()
    {
        $this->_testGetProductUrl('getAddUrl', '/catalog/product_compare/add/');
    }

    public function testGetAddToWishlistUrl()
    {
        $this->_testGetProductUrl('getAddToWishlistUrl', '/wishlist/index/add/');
    }

    public function testGetAddToCartUrl()
    {
        $this->_testGetProductUrl('getAddToCartUrl', '/checkout/cart/add/');
    }

    public function testGetRemoveUrl()
    {
        $this->_testGetProductUrl('getRemoveUrl', '/catalog/product_compare/remove/');
    }

    public function testGetClearListUrl()
    {
        $this->assertContains('/catalog/product_compare/clear/', $this->_helper->getClearListUrl());
    }

    /**
     * @see testGetListUrl() for coverage of customer case
     */
    public function testGetItemCollection()
    {
        $this->assertInstanceOf(
            'Magento_Catalog_Model_Resource_Product_Compare_Item_Collection', $this->_helper->getItemCollection()
        );
    }

    /**
     * calculate()
     * getItemCount()
     * hasItems()
     * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
     */
    public function testCalculate()
    {
         /** @var $session Magento_Catalog_Model_Session */
        $session = Mage::getSingleton('Magento_Catalog_Model_Session');
        try {
            $session->unsCatalogCompareItemsCount();
            $this->assertFalse($this->_helper->hasItems());
            $this->assertEquals(0, $session->getCatalogCompareItemsCount());

            $this->_populateCompareList();
            $this->_helper->calculate();
            $this->assertEquals(2, $session->getCatalogCompareItemsCount());
            $this->assertTrue($this->_helper->hasItems());

            $session->unsCatalogCompareItemsCount();
        } catch (Exception $e) {
            $session->unsCatalogCompareItemsCount();
            throw $e;
        }
    }

    public function testSetGetAllowUsedFlat()
    {
        $this->assertTrue($this->_helper->getAllowUsedFlat());
        $this->_helper->setAllowUsedFlat(false);
        $this->assertFalse($this->_helper->getAllowUsedFlat());
    }

    protected function _testGetProductUrl($method, $expectedFullAction)
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->setId(10);
        $url = $this->_helper->$method($product);
        $this->assertContains($expectedFullAction, $url);
        $this->assertContains('/product/10/', $url);
        $this->assertContains('/uenc/', $url);
    }

    /**
     * Add products from fixture to compare list
     */
    protected function _populateCompareList()
    {
        $productOne = Mage::getModel('Magento_Catalog_Model_Product');
        $productTwo = Mage::getModel('Magento_Catalog_Model_Product');
        $productOne->load(10);
        $productTwo->load(11);
        /** @var $compareList Magento_Catalog_Model_Product_Compare_List */
        $compareList = Mage::getModel('Magento_Catalog_Model_Product_Compare_List');
        $compareList->addProduct($productOne)->addProduct($productTwo);
    }
}
