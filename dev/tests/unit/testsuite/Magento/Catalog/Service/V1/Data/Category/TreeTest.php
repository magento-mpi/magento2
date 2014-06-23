<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\Category;

/**
 * Test for \Magento\Catalog\Service\V1\Category\ReadService
 */
class TreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Model\Resource\Category\Tree
     */
    protected $categoryTreeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Store\Model\StoreManager
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Service\V1\Data\Eav\Category\TreeBuilderFactory
     */
    protected $treeBuilderFactory;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Category\Tree
     */
    protected $tree;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->categoryTreeMock = $this->getMockBuilder(
                '\Magento\Catalog\Model\Resource\Category\Tree'
            )->disableOriginalConstructor()
            ->getMock();

        $this->categoryCollection = $this->getMockBuilder(
                '\Magento\Catalog\Model\Resource\Category\Collection'
            )->disableOriginalConstructor()
            ->getMock();

        $this->storeManagerMock = $this->getMockBuilder(
                '\Magento\Store\Model\StoreManager'
            )->disableOriginalConstructor()
            ->getMock();

        $this->treeBuilderFactory = $this->getMockBuilder(
                '\Magento\Catalog\Service\V1\Data\Eav\Category\TreeBuilderFactory'
            )->disableOriginalConstructor()
            ->getMock();

        $this->tree = $this->objectManager
            ->getObject(
                'Magento\Catalog\Service\V1\Data\Category\Tree',
                [
                    'categoryCollection' => $this->categoryCollection,
                    'categoryTree' => $this->categoryTreeMock,
                    'storeManager' => $this->storeManagerMock,
                    'treeBuilderFactory' => $this->treeBuilderFactory
                ]
            );

    }

    public function testGetNode()
    {
        $category = $this->getMockBuilder(
                '\Magento\Catalog\Model\Category'
            )->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->exactly(2))->method('getId')->will($this->returnValue(1));

        $node = $this->getMockBuilder(
                '\Magento\Framework\Data\Tree\Node'
            )->disableOriginalConstructor()
            ->getMock();
        $node->expects($this->once())->method('loadChildren');
        $this->categoryTreeMock->expects($this->once())->method('loadNode')
            ->with($this->equalTo(1))
            ->will($this->returnValue($node));

        $store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $store->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($store));

        $this->categoryCollection->expects($this->any())->method('addAttributeToSelect')->will($this->returnSelf());
        $this->categoryCollection->expects($this->once())->method('setProductStoreId')->will($this->returnSelf());
        $this->categoryCollection->expects($this->once())->method('setLoadProductCount')->will($this->returnSelf());
        $this->categoryCollection->expects($this->once())->method('setStoreId')->will($this->returnSelf());

        $this->categoryTreeMock->expects($this->once())->method('addCollectionData')
            ->with($this->equalTo($this->categoryCollection));
        $this->tree->getRootNode($category);
    }

    public function testGetRootNode()
    {
        $store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $store->expects($this->once())->method('getRootCategoryId')->will($this->returnValue(2));
        $store->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($store));

        $this->categoryCollection->expects($this->any())->method('addAttributeToSelect')->will($this->returnSelf());
        $this->categoryCollection->expects($this->once())->method('setProductStoreId')->will($this->returnSelf());
        $this->categoryCollection->expects($this->once())->method('setLoadProductCount')->will($this->returnSelf());
        $this->categoryCollection->expects($this->once())->method('setStoreId')->will($this->returnSelf());

        $node = $this->getMockBuilder(
            'Magento\Catalog\Model\Resource\Category\Tree'
        )->disableOriginalConstructor()
        ->getMock();
        $node->expects($this->once())->method('addCollectionData')
            ->with($this->equalTo($this->categoryCollection));
        $node->expects($this->once())->method('getNodeById')->with($this->equalTo(2));
        $this->categoryTreeMock->expects($this->once())->method('load')
            ->with($this->equalTo(null))
            ->will($this->returnValue($node));
        $this->tree->getRootNode();
    }
}
