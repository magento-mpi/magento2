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
 * Test class for \Magento\Catalog\Model\Product\Url.
 *
 * @magentoDataFixture Magento/Catalog/_files/url_rewrites.php
 */
class Magento_Catalog_Model_Product_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Url
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento\Catalog\Model\Product\Url');
    }

    public function testGetUrlInstance()
    {
        $instance = $this->_model->getUrlInstance();
        $this->assertInstanceOf('\Magento\Core\Model\Url', $instance);
        $this->assertSame($instance, $this->_model->getUrlInstance());
    }

    public function testGetUrlRewrite()
    {
        $instance = $this->_model->getUrlRewrite();
        $this->assertInstanceOf('\Magento\Core\Model\Url\Rewrite', $instance);
        $this->assertSame($instance, $this->_model->getUrlRewrite());
    }

    public function testGetUrlInStore()
    {
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);
        $this->assertStringEndsWith('simple-product.html', $this->_model->getUrlInStore($product));
    }

    public function testGetProductUrl()
    {
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);
        $this->assertStringEndsWith('simple-product.html', $this->_model->getProductUrl($product));
    }

    public function testFormatUrlKey()
    {
        $this->assertEquals('abc-test', $this->_model->formatUrlKey('AbC#-$^test'));
    }

    public function testGetUrlPath()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->setUrlPath('product.html');

        /** @var $category \Magento\Catalog\Model\Category */
        $category = Mage::getModel('Magento\Catalog\Model\Category');
        $category->setUrlPath('category.html');
        $this->assertEquals('product.html', $this->_model->getUrlPath($product));
        $this->assertEquals('category/product.html', $this->_model->getUrlPath($product, $category));
    }

    public function testGetUrl()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->load(1);
        $this->assertStringEndsWith('simple-product.html', $this->_model->getUrl($product));

        $product = Mage::getModel('Magento\Catalog\Model\Product');
        $product->setId(100);
        $this->assertStringEndsWith('catalog/product/view/id/100/', $this->_model->getUrl($product));
    }
}
