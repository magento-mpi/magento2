<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogUrlRewrite\Model;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;

class CategoryUrlPathGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManagerMock;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfigMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $categoryFactoryMock;

    /** @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject */
    protected $categoryMock;

    protected function setUp()
    {
        $categoryMethods = [
            '__wakeup',
            'getUrlPath',
            'getParentId',
            'getLevel',
            'dataHasChangedFor',
            'getUrlKey',
            'getStoreId',
            'getId',
            'formatUrlKey',
            'getName',
        ];
        $this->categoryMock = $this->getMock('Magento\Catalog\Model\Category', $categoryMethods, [], '', false);
        $this->storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->scopeConfigMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->categoryFactoryMock = $this->getMock('Magento\Catalog\Model\CategoryFactory', ['create']);

        $this->categoryUrlPathGenerator = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator',
            [
                'storeManager' => $this->storeManagerMock,
                'scopeConfig' => $this->scopeConfigMock,
                'categoryFactory' => $this->categoryFactoryMock
            ]
        );
    }

    public function getUrlPathDataProvider()
    {
        return [
            [1, 'url-path', 1, '', false, false, ''],
            [2, 'url-path', 2, '', false, false, 'url-path'],
            [2, 'url-path', 2, 'url-key', true, false, 'url-key'],
            [2, 'url-path', 2, 'url-key', false, true, 'url-key'],
            [null, 'url-path', 3, 'url-key', false, true, 'url-key'],
        ];
    }

    /**
     * @dataProvider getUrlPathDataProvider
     */
    public function testGetUrlPath(
        $parentId,
        $urlPath,
        $level,
        $urlKey,
        $dataChangedForUrlKey,
        $dataChangedForPathIds,
        $result
    ) {
        $this->categoryMock->expects($this->any())->method('getParentId')->will($this->returnValue($parentId));
        $this->categoryMock->expects($this->any())->method('getLevel')->will($this->returnValue($level));
        $this->categoryMock->expects($this->any())->method('getUrlPath')->will($this->returnValue($urlPath));
        $this->categoryMock->expects($this->any())->method('getUrlKey')->will($this->returnValue($urlKey));
        $this->categoryMock->expects($this->any())->method('dataHasChangedFor')
            ->will($this->returnValueMap([['url_key', $dataChangedForUrlKey], ['path_ids', $dataChangedForPathIds]]));

        $this->assertEquals($result, $this->categoryUrlPathGenerator->getUrlPath($this->categoryMock));
    }

    public function getUrlPathWithParentDataProvider()
    {
        return [
            ['url-key', 2, 'parent-category-path', 'parent-category-path/url-key'],
            ['url-key', 1, null, 'url-key'],
        ];
    }

    /**
     * @dataProvider getUrlPathWithParentDataProvider
     */
    public function testGetUrlPathWithParent($urlKey, $parentCategoryParentId, $parentUrlPath, $result)
    {
        $parentId = 3;
        $level = 3;
        $urlPath = null;
        $parentLevel = 2;
        $this->categoryMock->expects($this->any())->method('getParentId')->will($this->returnValue($parentId));
        $this->categoryMock->expects($this->any())->method('getLevel')->will($this->returnValue($level));
        $this->categoryMock->expects($this->any())->method('getUrlPath')->will($this->returnValue($urlPath));
        $this->categoryMock->expects($this->any())->method('getUrlKey')->will($this->returnValue($urlKey));
        $methods = ['__wakeup', 'getUrlPath', 'getParentId', 'getLevel', 'dataHasChangedFor', 'load'];
        $parentCategoryMock = $this->getMock('Magento\Catalog\Model\Category', $methods, [], '', false);
        $parentCategoryMock->expects($this->any())->method('getParentId')
            ->will($this->returnValue($parentCategoryParentId));
        $parentCategoryMock->expects($this->any())->method('getLevel')->will($this->returnValue($parentLevel));
        $parentCategoryMock->expects($this->any())->method('getUrlPath')->will($this->returnValue($parentUrlPath));
        $parentCategoryMock->expects($this->any())->method('load')->will($this->returnSelf());
        $parentCategoryMock->expects($this->any())->method('dataHasChangedFor')
            ->will($this->returnValueMap([['url_key', false], ['path_ids', false]]));

        $this->categoryFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($parentCategoryMock));

        $this->assertEquals($result, $this->categoryUrlPathGenerator->getUrlPath($this->categoryMock));
    }


    public function getUrlPathWithSuffixDataProvider()
    {
        return [
            ['url-path', 1, null, '.html', 'url-path.html'],
            ['url-path', null, 1, '.html', 'url-path.html'],
        ];
    }

    /**
     * @dataProvider getUrlPathWithSuffixDataProvider
     */
    public function testGetUrlPathWithSuffixAndStore($urlPath, $storeId, $categoryStoreId, $suffix, $result)
    {
        $this->categoryMock->expects($this->any())->method('getStoreId')->will($this->returnValue($categoryStoreId));
        $this->categoryMock->expects($this->once())->method('getParentId')->will($this->returnValue(2));
        $this->categoryMock->expects($this->once())->method('getUrlPath')->will($this->returnValue($urlPath));
        $this->categoryMock->expects($this->exactly(2))->method('dataHasChangedFor')
            ->will($this->returnValueMap([['url_key', false], ['path_ids', false]]));

        $passedStoreId = $storeId ? $storeId : $categoryStoreId;
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX, ScopeInterface::SCOPE_STORE, $passedStoreId)
            ->will($this->returnValue($suffix));

        $this->assertEquals(
            $result,
            $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->categoryMock, $storeId)
        );
    }

    public function testGetUrlPathWithSuffixWithoutStore()
    {
        $urlPath = 'url-path';
        $storeId = null;
        $currentStoreId = 1;
        $suffix = '.html';
        $result = 'url-path.html';

        $this->categoryMock->expects($this->any())->method('getStoreId')->will($this->returnValue($storeId));
        $this->categoryMock->expects($this->once())->method('getParentId')->will($this->returnValue(2));
        $this->categoryMock->expects($this->once())->method('getUrlPath')->will($this->returnValue($urlPath));
        $this->categoryMock->expects($this->exactly(2))->method('dataHasChangedFor')
            ->will($this->returnValueMap([['url_key', false], ['path_ids', false]]));

        $storeMock = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($currentStoreId));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));
        $this->scopeConfigMock->expects($this->once())->method('getValue')
            ->with(CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX, ScopeInterface::SCOPE_STORE, $currentStoreId)
            ->will($this->returnValue($suffix));

        $this->assertEquals(
            $result,
            $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->categoryMock, $storeId)
        );
    }

    public function testGetCanonicalUrlPath()
    {
        $this->categoryMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->assertEquals(
            'catalog/category/view/id/1',
            $this->categoryUrlPathGenerator->getCanonicalUrlPath($this->categoryMock)
        );
    }

    public function generateUrlKeyDataProvider()
    {
        return [
            ['url-key', null, 'url-key'],
            ['', 'category-name', 'category-name'],
        ];
    }

    /**
     * @dataProvider generateUrlKeyDataProvider
     */
    public function testGenerateUrlKey($urlKey, $name, $result)
    {
        $this->categoryMock->expects($this->once())->method('getUrlKey')->will($this->returnValue($urlKey));
        $this->categoryMock->expects($this->any())->method('getName')->will($this->returnValue($name));
        $this->categoryMock->expects($this->once())->method('formatUrlKey')->will($this->returnArgument(0));

        $this->assertEquals($result, $this->categoryUrlPathGenerator->generateUrlKey($this->categoryMock));
    }
}
