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
 * Test class for Magento_Catalog_Model_Product_Url.
 *
 * @magentoDataFixture Magento/Catalog/_files/url_rewrites.php
 */
class Magento_Catalog_Model_Product_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Product_Url
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Catalog_Model_Product_Url');
    }

    public function testGetUrlInstance()
    {
        $instance = $this->_model->getUrlInstance();
        $this->assertInstanceOf('Magento_Core_Model_Url', $instance);
        $this->assertSame($instance, $this->_model->getUrlInstance());
    }

    public function testGetUrlRewrite()
    {
        $instance = $this->_model->getUrlRewrite();
        $this->assertInstanceOf('Magento_Core_Model_Url_Rewrite', $instance);
        $this->assertSame($instance, $this->_model->getUrlRewrite());
    }

    public function testGetUrlInStore()
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);
        $this->assertStringEndsWith('simple-product.html', $this->_model->getUrlInStore($product));
    }

    public function testGetProductUrl()
    {
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);
        $this->assertStringEndsWith('simple-product.html', $this->_model->getProductUrl($product));
    }

    public function testFormatUrlKey()
    {
        $this->assertEquals('abc-test', $this->_model->formatUrlKey('AbC#-$^test'));
    }

    public function testGetUrlPath()
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->setUrlPath('product.html');

        /** @var $category Magento_Catalog_Model_Category */
        $category = Mage::getModel('Magento_Catalog_Model_Category');
        $category->setUrlPath('category.html');
        $this->assertEquals('product.html', $this->_model->getUrlPath($product));
        $this->assertEquals('category/product.html', $this->_model->getUrlPath($product, $category));
    }

    public function testGetUrl()
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(1);
        $this->assertStringEndsWith('simple-product.html', $this->_model->getUrl($product));

        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->setId(100);
        $this->assertStringEndsWith('catalog/product/view/id/100/', $this->_model->getUrl($product));
    }
}
