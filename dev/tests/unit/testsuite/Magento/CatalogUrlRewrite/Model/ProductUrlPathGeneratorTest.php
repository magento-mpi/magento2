<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogUrlRewrite\Model;

use Magento\TestFramework\Helper\ObjectManager;
use \Magento\Store\Model\ScopeInterface;

class ProductUrlPathGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerMock;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfigMock;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator|\PHPUnit_Framework_MockObject_MockObject */
    protected $categoryUrlPathGeneratorMock;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $productMock;

    /** @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject */
    protected $categoryMock;

    protected function setUp()
    {
        $this->categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
        $productMethods = ['__wakeup', 'getData', 'getUrlKey', 'getName', 'formatUrlKey', 'getId'];
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', $productMethods, [], '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->categoryUrlPathGeneratorMock = $this->getMock(
            'Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator',
            [],
            [],
            '',
            false
        );

        $this->productUrlPathGenerator = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator',
            [
                'storeManager' => $this->storeManagerMock,
                'scopeConfig' => $this->scopeConfigMock,
                'categoryUrlPathGenerator' => $this->categoryUrlPathGeneratorMock
            ]
        );
    }

    public function getUrlPathDataProvider()
    {
        return [
            'path based on url path' => ['url-path', null, null, 'url-path'],
            'path based on url key' => [null, 'url-key', null, 'url-key'],
            'path based on product name 1' => [null, '', 'product-name', 'product-name'],
            'path based on product name 2' => [null, null, 'product-name', 'product-name'],
        ];
    }

    /**
     * @dataProvider getUrlPathDataProvider
     */
    public function testGetUrlPath($urlPath, $urlKey, $productName, $result)
    {
        $this->productMock->expects($this->any())->method('getData')->with('url_path')
            ->will($this->returnValue($urlPath));
        $this->productMock->expects($this->any())->method('getUrlKey')->will($this->returnValue($urlKey));
        $this->productMock->expects($this->any())->method('getName')->will($this->returnValue($productName));
        $this->productMock->expects($this->any())->method('formatUrlKey')->will($this->returnArgument(0));

        $this->assertEquals($result, $this->productUrlPathGenerator->getUrlPath($this->productMock, null));
    }

    public function testGetUrlPathWithCategory()
    {
        $this->productMock->expects($this->once())->method('getData')->with('url_path')
            ->will($this->returnValue('product-path'));
        $this->categoryUrlPathGeneratorMock->expects($this->once())->method('getUrlPath')
            ->will($this->returnValue('category-url-path'));

        $this->assertEquals(
            'category-url-path/product-path',
            $this->productUrlPathGenerator->getUrlPath($this->productMock, $this->categoryMock)
        );
    }

    public function testGetUrlPathWithSuffix()
    {
        $storeId = 1;
        $this->productMock->expects($this->once())->method('getData')->with('url_path')
            ->will($this->returnValue('product-path'));
        $storeMock = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX, ScopeInterface::SCOPE_STORE, $storeId)
            ->will($this->returnValue('.html'));

        $this->assertEquals(
            'product-path.html',
            $this->productUrlPathGenerator->getUrlPathWithSuffix($this->productMock, null)
        );
    }

    public function testGetUrlPathWithSuffixAndCategoryAnsStore()
    {
        $storeId = 1;
        $this->productMock->expects($this->once())->method('getData')->with('url_path')
            ->will($this->returnValue('product-path'));
        $this->categoryUrlPathGeneratorMock->expects($this->once())->method('getUrlPath')
            ->will($this->returnValue('category-url-path'));
        $this->storeManagerMock->expects($this->never())->method('getStore');
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX, ScopeInterface::SCOPE_STORE, $storeId)
            ->will($this->returnValue('.html'));

        $this->assertEquals(
            'category-url-path/product-path.html',
            $this->productUrlPathGenerator->getUrlPathWithSuffix($this->productMock, $storeId, $this->categoryMock)
        );
    }

    public function testGetCanonicalUrlPath()
    {
        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue(1));

        $this->assertEquals(
            'catalog/product/view/id/1',
            $this->productUrlPathGenerator->getCanonicalUrlPath($this->productMock)
        );
    }

    public function testGetCanonicalUrlPathWithCategory()
    {
        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->categoryMock->expects($this->any())->method('getId')->will($this->returnValue(1));

        $this->assertEquals(
            'catalog/product/view/id/1/category/1',
            $this->productUrlPathGenerator->getCanonicalUrlPath($this->productMock, $this->categoryMock)
        );
    }
}
