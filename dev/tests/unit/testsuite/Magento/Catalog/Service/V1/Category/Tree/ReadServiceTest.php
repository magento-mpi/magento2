<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Category\Tree;

/**
 * Test for \Magento\Catalog\Service\V1\Category\Tree\ReadService
 */
class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Service\V1\Data\Category\Tree
     */
    protected $categoryTreeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactoryMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Service\V1\Category\Tree\ReadService
     */
    protected $categoryService;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->categoryTreeMock = $this->getMockBuilder(
                'Magento\Catalog\Service\V1\Data\Category\Tree'
            )->disableOriginalConstructor()
            ->getMock();

        $this->categoryFactoryMock = $this->getMockBuilder(
                '\Magento\Catalog\Model\CategoryFactory'
            )->disableOriginalConstructor()
            ->setMethods(['create', 'load'])
            ->getMock();

        $this->categoryService = $this->objectManager
            ->getObject(
                '\Magento\Catalog\Service\V1\Category\Tree\ReadService',
                [
                    'categoryFactory' => $this->categoryFactoryMock,
                    'categoryTree' => $this->categoryTreeMock,
                ]
            );

    }

    /**
     * @dataProvider treeDataProvider
     */
    public function testTree($rootCategoryId, $depth)
    {
        $rootNode = $this->getMockBuilder(
            'Magento\Framework\Data\Tree\Node'
        )->disableOriginalConstructor()
        ->getMock();

        $category = null;
        if (!is_null($rootCategoryId)) {
            $category = $this->getMockBuilder(
                '\Magento\Catalog\Model\Category'
            )->disableOriginalConstructor()
            ->getMock();

            $category->expects($this->once())->method('getId')->will($this->returnValue($rootCategoryId));

            $this->categoryFactoryMock->expects($this->once())->method('create')->will($this->returnSelf());
            $this->categoryFactoryMock->expects($this->once())->method('load')
                ->with($this->equalTo($rootCategoryId))
                ->will($this->returnValue($category));
        }
        $this->categoryTreeMock->expects($this->once())->method('getRootNode')
            ->with($this->equalTo($category))
            ->will($this->returnValue($rootNode));

        $this->categoryTreeMock->expects($this->once())->method('getTree')
            ->with($this->equalTo($rootNode), $this->equalTo($depth));
        $this->categoryService->tree($rootCategoryId, $depth);
    }

    /**
     * @return array
     */
    public function treeDataProvider()
    {
        return array(
            [1, 0],
            [null, 3]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testTreeAbsentCategory()
    {
        $category = $this->getMockBuilder(
            '\Magento\Catalog\Model\Category'
        )->disableOriginalConstructor()
        ->getMock();

        $category->expects($this->once())->method('getId')->will($this->returnValue(null));
        $category->expects($this->never())->method('getPathIds');

        $this->categoryFactoryMock->expects($this->once())->method('create')->will($this->returnSelf());
        $this->categoryFactoryMock->expects($this->once())->method('load')
            ->with($this->equalTo(1))
            ->will($this->returnValue($category));

        $this->categoryService->tree(1);
    }
}
