<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

/**
 * Test class for \Magento\Catalog\Model\Product\Url.
 *
 * @magentoDataFixture Magento/Catalog/_files/url_rewrites.php
 * @magentoAppArea frontend
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Url
     */
    protected $_model;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator
     */
    protected $urlPathGenerator;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product\Url'
        );
        $this->urlPathGenerator = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator'
        );
    }

    public function testGetUrlInstance()
    {
        $instance = $this->_model->getUrlInstance();
        $this->assertInstanceOf('Magento\Framework\Url', $instance);
        $this->assertSame($instance, $this->_model->getUrlInstance());
    }

    public function testGetUrlInStore()
    {
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->load(1);
        $this->assertStringEndsWith('simple-product.html', $this->_model->getUrlInStore($product));
    }

    public function testGetProductUrl()
    {
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
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
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->setUrlPath('product.html');

        /** @var $category \Magento\Catalog\Model\Category */
        $category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Category',
            ['data' => ['url_path' => 'category', 'entity_id' => 5, 'path_ids' => [2,3,5]]]
        );
        $category->setOrigData();

        $this->assertEquals('product.html', $this->urlPathGenerator->getUrlPath($product));
        $this->assertEquals('category/product.html', $this->urlPathGenerator->getUrlPath($product, $category));
    }

    public function testGetUrl()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->load(1);
        $this->assertStringEndsWith('simple-product.html', $this->_model->getUrl($product));

        $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\Product'
        );
        $product->setId(100);
        $this->assertStringEndsWith('catalog/product/view/id/100/', $this->_model->getUrl($product));
    }
}
