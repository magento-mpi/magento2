<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

use Magento\TestFramework\Helper\ObjectManager;

class CategoryRegistryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\CategoryRegistryFactory */
    protected $categoryRegistryFactory;

    /** @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager');

        $this->categoryRegistryFactory = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\CategoryRegistryFactory',
            [
                'objectManager' => $this->objectManager
            ]
        );
    }

    public function testCreate()
    {
        $categoriesCollection = $this->getMockBuilder('Magento\Catalog\Model\Resource\Category\Collection')
            ->disableOriginalConstructor()->getMock();
        $categoriesCollection->expects($this->any())->method('addAttributeToSelect')->will($this->returnSelf());
        $category = $this->getMockBuilder('Magento\Catalog\Model\Category')->disableOriginalConstructor()->getMock();
        $category->expects($this->any())->method('getId')->will($this->returnValue(1));
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')->disableOriginalConstructor()->getMock();
        $product->expects($this->any())->method('getCategoryCollection')
            ->will($this->returnValue($categoriesCollection));
        $categoriesCollection->expects($this->any())->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$category])));

        $categoryRegistry = $this->getMockBuilder('Magento\CatalogUrlRewrite\Model\CategoryRegistry')
            ->disableOriginalConstructor()->getMock();
        $this->objectManager->expects($this->once())->method('create')->will($this->returnValue($categoryRegistry));
        $this->assertEquals(
            $categoryRegistry,
            $this->categoryRegistryFactory->create($product)
        );
    }
}
