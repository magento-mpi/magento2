<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\TestFramework\Helper\ObjectManager;

class ChildrenUrlRewriteGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\Category\ChildrenUrlRewriteGenerator */
    protected $childrenUrlRewriteGenerator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $category;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $categoryUrlRewriteGeneratorFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $categoryUrlRewriteGenerator;

    protected function setUp()
    {
        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()->getMock();
        $this->categoryUrlRewriteGeneratorFactory = $this->getMockBuilder(
            'Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGeneratorFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $this->categoryUrlRewriteGenerator = $this->getMockBuilder(
            'Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator'
        )->disableOriginalConstructor()->getMock();
        $this->childrenUrlRewriteGenerator = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\Category\ChildrenUrlRewriteGenerator',
            [
                'categoryUrlRewriteGeneratorFactory' => $this->categoryUrlRewriteGeneratorFactory
            ]
        );
    }

    public function testNoChildrenCategories()
    {
        $this->category->expects($this->once())->method('getChildrenCategories')->will($this->returnValue([]));

        $this->assertEquals([], $this->childrenUrlRewriteGenerator->generate('store_id', $this->category));
    }

    public function testGenerate()
    {
        $storeId = 'store_id';
        $saveRewritesHistory = 'flag';

        $childCategory = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()->getMock();
        $childCategory->expects($this->once())->method('setStoreId')->with($storeId);
        $childCategory->expects($this->once())->method('setData')
            ->with('save_rewrites_history', $saveRewritesHistory);
        $this->category->expects($this->once())->method('getChildrenCategories')
            ->will($this->returnValue([$childCategory]));
        $this->category->expects($this->any())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue($saveRewritesHistory));
        $this->categoryUrlRewriteGeneratorFactory->expects($this->once())->method('create')
            ->will($this->returnValue($this->categoryUrlRewriteGenerator));
        $this->categoryUrlRewriteGenerator->expects($this->once())->method('generate')->with($childCategory)
            ->will($this->returnValue([['url-1', 'url-2']]));

        $this->assertEquals(
            [['url-1', 'url-2']],
            $this->childrenUrlRewriteGenerator->generate($storeId, $this->category)
        );
    }
}
