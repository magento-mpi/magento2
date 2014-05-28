<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

class ProductAttributeSetAttributeServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductAttributeSetAttributeService
     */
    private $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeGroupMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeSetMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityTypeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrResourceMock;

    /**
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    protected function setUp()
    {
        $attributeFactoryMock = $this->getMock(
            '\Magento\Eav\Model\Entity\AttributeFactory', array('create'), array(), '', false
        );
        $setFactoryMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\SetFactory', array('create'), array(), '', false
        );
        $groupFactoryMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\GroupFactory', array('create'), array(), '', false
        );
        $entityTypeFactoryMock = $this->getMock(
            '\Magento\Eav\Model\ConfigFactory', array('create'), array(), '', false
        );

        $this->attributeMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute',
            array(
                'getId', 'setId', 'setEntityTypeId', 'setAttributeSetId', 'load',
                'setAttributeGroupId', 'setSortOrder', 'loadEntityAttributeIdBySet', '__sleep', '__wakeup'
            ),
            array(), '', false
        );
        $this->attributeGroupMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Group', array(), array(), '', false
        );
        $this->attributeSetMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set', array(), array(), '', false
        );
        $this->entityTypeConfigMock = $this->getMock(
            '\Magento\Eav\Model\Config', array('getEntityType', 'getEntityTypeCode', 'getId', '__sleep', '__wakeup'),
            array(), '', false
        );
        $this->attrResourceMock = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute', array(), array(), '', false
        );

        $attributeFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->attributeMock));
        $setFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->attributeSetMock));
        $groupFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->attributeGroupMock));
        $entityTypeFactoryMock->expects($this->any())
            ->method('create')->will($this->returnValue($this->entityTypeConfigMock));

        $this->service = new ProductAttributeSetAttributeService(
            $attributeFactoryMock,
            $groupFactoryMock,
            $setFactoryMock,
            $entityTypeFactoryMock,
            $this->attrResourceMock
        );
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::__construct
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     */
    public function testAddAttribute()
    {
        $data = [
            'attribute_id'       => 1,
            'attribute_group_id' => 1,
            'sort_order'         => 1
        ];

        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray($data)->create();

        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $objectMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $objectMock->expects($this->any())->method('getData')->will($this->returnValue(1));

        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->attributeGroupMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->attributeMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));

        $this->entityTypeConfigMock->expects($this->once())->method('getEntityType')->will($this->returnSelf());
        $this->entityTypeConfigMock->expects($this->once())
            ->method('getEntityTypeCode')->will($this->returnValue(\Magento\Catalog\Model\Product::ENTITY));
        $this->entityTypeConfigMock->expects($this->once())->method('getId')->will($this->returnValue(4));

        $this->attributeMock->expects($this->once())->method('setId')->with(1);
        $this->attributeMock->expects($this->once())->method('setEntityTypeId')->with(4);
        $this->attributeMock->expects($this->once())->method('setAttributeSetId')->with(1);
        $this->attributeMock->expects($this->once())->method('setAttributeGroupId')->with(1);
        $this->attributeMock->expects($this->once())->method('setSortOrder')->with(1);
        $this->attributeMock->expects($this->once())
            ->method('loadEntityAttributeIdBySet')->will($this->returnValue($objectMock));
        $this->attrResourceMock->expects($this->once())->method('saveInSetIncluding')->with($this->attributeMock);

        $this->service->addAttribute(1, $attributeDataObject);
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Attribute set does not exist
     */
    public function testAddAttributeWithWrongAttributeSet()
    {
        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray([])->create();

        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->service->addAttribute(1, $attributeDataObject);
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Wrong attribute set id provided
     */
    public function testAddAttributeWithAttributeSetOfOtherEntityType()
    {
        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray(['attribute_group'])->create();

        $attributeSetMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $attributeSetMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($attributeSetMock));

        $this->entityTypeConfigMock->expects($this->once())->method('getEntityType')->will($this->returnSelf());
        $this->entityTypeConfigMock->expects($this->once())->method('getEntityTypeCode')->will($this->returnValue('0'));

        $this->service->addAttribute(1, $attributeDataObject);
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Attribute group does not exist
     */
    public function testAddAttributeWithWrongAttributeGroup()
    {
        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray(['attribute_group'])->create();

        $attributeSetMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $attributeSetMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($attributeSetMock));

        $entityCode = \Magento\Catalog\Model\Product::ENTITY;
        $this->entityTypeConfigMock->expects($this->once())->method('getEntityType')->will($this->returnSelf());
        $this->entityTypeConfigMock->expects($this->once())
            ->method('getEntityTypeCode')->will($this->returnValue($entityCode));

        $attributeGroupMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $this->attributeGroupMock->expects($this->once())->method('load')
            ->will($this->returnValue($attributeGroupMock));

        $this->service->addAttribute(1, $attributeDataObject);
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Attribute does not exist
     */
    public function testAddAttributeWithWrongAttribute()
    {
        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray(['attribute_group'])->create();

        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $objectMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->attributeGroupMock->expects($this->once())->method('load') ->will($this->returnValue($objectMock));

        $entityCode = \Magento\Catalog\Model\Product::ENTITY;
        $this->entityTypeConfigMock->expects($this->once())->method('getEntityType')->will($this->returnSelf());
        $this->entityTypeConfigMock->expects($this->once())
            ->method('getEntityTypeCode')->will($this->returnValue($entityCode));

        $attributeMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $this->attributeMock->expects($this->once())->method('load')->will($this->returnValue($attributeMock));

        $this->service->addAttribute(1, $attributeDataObject);
    }

    public function testSuccessfullyDeleteAttribute()
    {
        $methods = array('__wakeup', 'setAttributeSetId',
            'loadEntityAttributeIdBySet', 'getEntityAttributeId', 'deleteEntity', 'getId');
        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $objectMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $attributeMock =
            $this->getMock('Magento\Eav\Model\Entity\Attribute\AbstractAttribute', $methods, array(), '', false);
        $this->attributeMock->expects($this->once())->method('load')->will($this->returnValue($attributeMock));
        $attributeMock->expects($this->any())->method('getId')->will($this->returnValue(2));
        $attributeMock->expects($this->once())->method('setAttributeSetId')->with(1)->will($this->returnSelf());
        $attributeMock->expects($this->once())->method('loadEntityAttributeIdBySet')->will($this->returnSelf());
        $attributeMock->expects($this->once())->method('getEntityAttributeId')->will($this->returnValue(10));
        $attributeMock->expects($this->once())->method('deleteEntity');
        $this->assertEquals(true, $this->service->deleteAttribute('attributeSetId', 'attributeId'));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with attributeSetId = attributeSetId
     */
    public function testDeleteAttributeFromNonExistingAttributeSet()
    {
        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $objectMock->expects($this->any())->method('getId')->will($this->returnValue(false));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->attributeMock->expects($this->never())->method('load');

        $this->service->deleteAttribute('attributeSetId', 'attributeId');
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with attributeId = attributeId
     */
    public function testDeleteNonExistingAttribute()
    {
        $methods = array('__wakeup', 'setAttributeSetId',
            'loadEntityAttributeIdBySet', 'getEntityAttributeId', 'deleteEntity', 'getId');
        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $objectMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $attributeMock =
            $this->getMock('Magento\Eav\Model\Entity\Attribute\AbstractAttribute', $methods, array(), '', false);
        $this->attributeMock->expects($this->once())->method('load')->will($this->returnValue($attributeMock));
        $attributeMock->expects($this->any())->method('getId')->will($this->returnValue(false));
        $attributeMock->expects($this->never())->method('setAttributeSetId');
        $this->service->deleteAttribute('attributeSetId', 'attributeId');
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Requested attribute is not in requested attribute set.
     */
    public function testDeleteAttributeNotInAttributeSet()
    {
        $methods = array('__wakeup', 'setAttributeSetId',
            'loadEntityAttributeIdBySet', 'getEntityAttributeId', 'deleteEntity', 'getId');
        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $objectMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $attributeMock =
            $this->getMock('Magento\Eav\Model\Entity\Attribute\AbstractAttribute', $methods, array(), '', false);
        $this->attributeMock->expects($this->once())->method('load')->will($this->returnValue($attributeMock));
        $attributeMock->expects($this->any())->method('getId')->will($this->returnValue(2));
        $attributeMock->expects($this->once())->method('setAttributeSetId')->with(1)->will($this->returnSelf());
        $attributeMock->expects($this->once())->method('loadEntityAttributeIdBySet')->will($this->returnSelf());
        $attributeMock->expects($this->once())->method('getEntityAttributeId')->will($this->returnValue(false));
        $attributeMock->expects($this->never())->method('deleteEntity');
        $this->assertEquals(true, $this->service->deleteAttribute('attributeSetId', 'attributeId'));
    }
}
