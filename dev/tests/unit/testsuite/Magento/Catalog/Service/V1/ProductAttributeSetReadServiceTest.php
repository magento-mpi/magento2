<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

class ProductAttributeSetReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\ProductAttributeSetReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $setFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $builderMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->setFactoryMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\SetFactory',
            array(), array(), '', false);
        $this->collectionFactoryMock = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory',
            array('create'), array(), '', false
        );
        $this->productFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory', array('create'), array(), '', false
        );
        $this->builderMock = $this->getMock('\Magento\Catalog\Service\V1\Data\Eav\AttributeSetBuilder',
            array('create', 'setId', 'setName', 'setSortOrder'), array(), '', false);

        $this->service = $objectManager->getObject('\Magento\Catalog\Service\V1\ProductAttributeSetReadService',
            array(
                'setFactory' => $this->setFactoryMock,
                'setCollectionFactory' => $this->collectionFactoryMock,
                'productFactory' => $this->productFactoryMock,
                'attributeSetBuilder' => $this->builderMock
            )
        );
    }

    public function testGetList()
    {
        $attributeSetData = array('attribute_set_id' => 4, 'attribute_set_name' => 'Default', 'sort_order' => 2);

        $collectionMock = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection', array(), array(), '', false
        );

        $productEntityId = 4;
        $productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->productFactoryMock->expects($this->once())->method('create')->will($this->returnValue($productMock));
        $resourceMock = $this->getMock('\Magento\Catalog\Model\Resource\Product', array(), array(), '', false);
        $productMock->expects($this->once())->method('getResource')->will($this->returnValue($resourceMock));
        $resourceMock->expects($this->once())->method('getTypeId')->will($this->returnValue($productEntityId));

        $this->collectionFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($collectionMock));

        $collectionMock->expects($this->once())->method('setEntityTypeFilter')
            ->with($productEntityId)
            ->will($this->returnSelf());

        $collectionMock->expects($this->once())->method('load')->will($this->returnSelf());

        $attributeSets = $resultSets = array();

        //prepare getter checks
        $setMock = $this->getMock('\Magento\Eav\Model\Resource\Entity\Attribute\Set',
            array('getId', 'getAttributeSetName', 'getSortOrder', '__wakeup'), array(), '', false);
        $setMock->expects($this->any())->method('getId')
            ->will($this->returnValue($attributeSetData['attribute_set_id']));
        $setMock->expects($this->any())->method('getAttributeSetName')
            ->will($this->returnValue($attributeSetData['attribute_set_name']));
        $setMock->expects($this->any())->method('getSortOrder')
            ->will($this->returnValue($attributeSetData['sort_order']));
        $attributeSets[] = $setMock;

        //prepare setter checks
        $this->builderMock->expects($this->once())->method('setId')
            ->with($attributeSetData['attribute_set_id']);
        $this->builderMock->expects($this->once())->method('setName')
            ->with($attributeSetData['attribute_set_name']);
        $this->builderMock->expects($this->once())->method('setSortOrder')
            ->with($attributeSetData['sort_order']);

        $dataObjectMock = $this->getMock(
            'Magento\Catalog\Service\V1\Data\Eav\AttributeSet', array(), array(), '', false
        );
        $this->builderMock->expects($this->once())->method('create')->will($this->returnValue($dataObjectMock));
        $resultSets[] = $dataObjectMock;

        $collectionMock->expects($this->any())->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator($attributeSets)));

        $this->assertEquals($resultSets, $this->service->getList());
    }
}