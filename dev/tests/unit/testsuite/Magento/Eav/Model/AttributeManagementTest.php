<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Eav\Model;
 
class AttributeManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AttributeManagement
     */
    protected $model;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $setRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityTypeFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeResourceMock;

    protected function setUp()
    {
        $this->setRepositoryMock =
            $this->getMock('Magento\Eav\Api\AttributeSetRepositoryInterface', [], [], '', false);
        $this->attributeBuilderMock =
            $this->getMock(
                'Magento\Eav\Api\Data\AttributeDataBuilder',
                [
                    'populateWithArray',
                    'create',
                    '__wakeup'
                ],
                [],
                '',
                false);
        $this->attributeCollectionMock =
            $this->getMock('Magento\Eav\Model\Resource\Entity\Attribute\Collection', [], [], '', false);
        $this->eavConfigMock =
            $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->entityTypeFactoryMock =
            $this->getMock('Magento\Eav\Model\ConfigFactory', ['create', '__wakeup'], [], '', false);
        $this->groupRepositoryMock =
            $this->getMock('Magento\Eav\Api\AttributeGroupRepositoryInterface', [], [], '', false);
        $this->attributeRepositoryMock =
            $this->getMock('Magento\Eav\Api\AttributeRepositoryInterface', [], [], '', false);
        $this->attributeResourceMock =
            $this->getMock('Magento\Eav\Model\Resource\Entity\Attribute', [], [], '', false);

        $this->model = new AttributeManagement(
            $this->setRepositoryMock,
            $this->attributeBuilderMock,
            $this->attributeCollectionMock,
            $this->eavConfigMock,
            $this->entityTypeFactoryMock,
            $this->groupRepositoryMock,
            $this->attributeRepositoryMock,
            $this->attributeResourceMock
        );
    }

    /**
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage AttributeSet with id "2" does not exist.
     */
    public function testAssignNoSuchEntityException()
    {
        $entityTypeCode= 1;
        $attributeSetId = 2;
        $attributeGroupId = 3;
        $attributeCode = 4;
        $sortOrder = 5;

        $this->setRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->will($this->throwException(new \Magento\Framework\Exception\NoSuchEntityException));

        $this->model->assign($entityTypeCode, $attributeSetId, $attributeGroupId, $attributeCode, $sortOrder);
    }

    /**
     *
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Wrong attribute set id provided
     */
    public function testAssignInputException()
    {
        $entityTypeCode= 1;
        $attributeSetId = 2;
        $attributeGroupId = 3;
        $attributeCode = 4;
        $sortOrder = 5;
        $attributeSetMock = $this->getMock('Magento\Eav\Api\Data\AttributeSetInterface', [], [], '', false);
        $this->setRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->willReturn($attributeSetMock);
        $this->entityTypeFactoryMock->expects($this->once())->method('create')->willReturn($this->eavConfigMock);
        $attributeSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(66);
        $entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $this->eavConfigMock->expects($this->once())->method('getEntityType')->with(66)->willReturn($entityTypeMock);
        $entityTypeMock->expects($this->once())->method('getEntityTypeCode')->willReturn($entityTypeCode+1);

        $this->model->assign($entityTypeCode, $attributeSetId, $attributeGroupId, $attributeCode, $sortOrder);
    }

    public function testAssign()
    {
        $entityTypeCode= 1;
        $attributeSetId = 2;
        $attributeGroupId = 3;
        $attributeCode = 4;
        $sortOrder = 5;
        $attributeSetMock = $this->getMock('Magento\Eav\Api\Data\AttributeSetInterface', [], [], '', false);
        $this->setRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->willReturn($attributeSetMock);
        $this->entityTypeFactoryMock->expects($this->once())->method('create')->willReturn($this->eavConfigMock);
        $attributeSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(66);
        $entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $this->eavConfigMock->expects($this->once())->method('getEntityType')->with(66)->willReturn($entityTypeMock);
        $entityTypeMock->expects($this->once())->method('getEntityTypeCode')->willReturn($entityTypeCode);
        $this->groupRepositoryMock->expects($this->once())->method('get')->with($attributeGroupId);
        $attributeMock = $this->getMock('Magento\Eav\Model\Attribute', [], [], '', false);
        $this->attributeRepositoryMock->expects($this->once())
            ->method('get')
            ->with($entityTypeCode, $attributeCode)
            ->willReturn($attributeMock);
        $attributeMock->expects($this->once())->method('getAttributeId')->willReturn(16);
        $this->attributeResourceMock->expects($this->once())->method('saveInSetIncluding')
            ->with(
                $attributeMock,
                16,
                $attributeSetId,
                $attributeGroupId,
                $sortOrder
            )
            ->willReturn($attributeMock);
        $attributeMock->expects($this->once())->method('setAttributeSetId')->with($attributeSetId)->willReturnSelf();
        $attributeMock->expects($this->once())->method('loadEntityAttributeIdBySet')->willReturnSelf();
        $attributeMock->expects($this->once())->method('getData')->with('entity_attribute_id')->willReturnSelf();

        $this->assertEquals(
            $attributeMock,
            $this->model->assign($entityTypeCode, $attributeSetId, $attributeGroupId, $attributeCode, $sortOrder)
        );
    }

    public function testUnassign()
    {
        $attributeSetId = 1;
        $attributeCode = 'code';

        $attributeSetMock = $this->getMock('Magento\Eav\Api\Data\AttributeSetInterface', [], [], '', false);
        $this->setRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->willReturn($attributeSetMock);
        $this->entityTypeFactoryMock->expects($this->once())->method('create')->willReturn($this->eavConfigMock);
        $attributeSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(66);
        $entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $this->eavConfigMock->expects($this->once())->method('getEntityType')->with(66)->willReturn($entityTypeMock);
        $attributeMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute',
            [
                'getEntityAttributeId',
                'setAttributeSetId',
                'loadEntityAttributeIdBySet',
                'getIsUserDefined',
                'deleteEntity',
                '__wakeup'
            ],
            [],
            '',
            false);
        $entityTypeMock->expects($this->once())->method('getEntityTypeCode')->willReturn('entity type code');
        $this->attributeRepositoryMock->expects($this->once())
            ->method('get')
            ->with('entity type code', $attributeCode)
            ->willReturn($attributeMock);
        $attributeSetMock->expects($this->once())->method('getId')->willReturn(33);
        $attributeMock->expects($this->once())->method('setAttributeSetId')->with(33)->willReturnSelf();
        $attributeMock->expects($this->once())->method('loadEntityAttributeIdBySet')->willReturnSelf();
        $attributeMock->expects($this->once())->method('getEntityAttributeId')->willReturn(12);
        $attributeMock->expects($this->once())->method('getIsUserDefined')->willReturn(true);
        $attributeMock->expects($this->once())->method('deleteEntity')->willReturnSelf();

        $this->assertTrue($this->model->unassign($attributeSetId, $attributeCode));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Requested attribute is not in requested attribute set.
     */
    public function testUnassignInputException()
    {
        $attributeSetId = 1;
        $attributeCode = 'code';

        $attributeSetMock = $this->getMock('Magento\Eav\Api\Data\AttributeSetInterface', [], [], '', false);
        $this->setRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->willReturn($attributeSetMock);
        $this->entityTypeFactoryMock->expects($this->once())->method('create')->willReturn($this->eavConfigMock);
        $attributeSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(66);
        $entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $this->eavConfigMock->expects($this->once())->method('getEntityType')->with(66)->willReturn($entityTypeMock);
        $attributeMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute',
            [
                'getEntityAttributeId',
                'setAttributeSetId',
                'loadEntityAttributeIdBySet',
                'getIsUserDefined',
                'deleteEntity',
                '__wakeup'
            ],
            [],
            '',
            false);
        $entityTypeMock->expects($this->once())->method('getEntityTypeCode')->willReturn('entity type code');
        $this->attributeRepositoryMock->expects($this->once())
            ->method('get')
            ->with('entity type code', $attributeCode)
            ->willReturn($attributeMock);
        $attributeSetMock->expects($this->once())->method('getId')->willReturn(33);
        $attributeMock->expects($this->once())->method('setAttributeSetId')->with(33)->willReturnSelf();
        $attributeMock->expects($this->once())->method('loadEntityAttributeIdBySet')->willReturnSelf();
        $attributeMock->expects($this->once())->method('getEntityAttributeId')->willReturn(null);
        $attributeMock->expects($this->never())->method('getIsUserDefined');
        $attributeMock->expects($this->never())->method('deleteEntity');

        $this->model->unassign($attributeSetId, $attributeCode);
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage System attribute can not be deleted
     */
    public function testUnassignStateException()
    {
        $attributeSetId = 1;
        $attributeCode = 'code';

        $attributeSetMock = $this->getMock('Magento\Eav\Api\Data\AttributeSetInterface', [], [], '', false);
        $this->setRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->willReturn($attributeSetMock);
        $this->entityTypeFactoryMock->expects($this->once())->method('create')->willReturn($this->eavConfigMock);
        $attributeSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(66);
        $entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $this->eavConfigMock->expects($this->once())->method('getEntityType')->with(66)->willReturn($entityTypeMock);
        $attributeMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute',
            [
                'getEntityAttributeId',
                'setAttributeSetId',
                'loadEntityAttributeIdBySet',
                'getIsUserDefined',
                'deleteEntity',
                '__wakeup'
            ],
            [],
            '',
            false);
        $entityTypeMock->expects($this->once())->method('getEntityTypeCode')->willReturn('entity type code');
        $this->attributeRepositoryMock->expects($this->once())
            ->method('get')
            ->with('entity type code', $attributeCode)
            ->willReturn($attributeMock);
        $attributeSetMock->expects($this->once())->method('getId')->willReturn(33);
        $attributeMock->expects($this->once())->method('setAttributeSetId')->with(33)->willReturnSelf();
        $attributeMock->expects($this->once())->method('loadEntityAttributeIdBySet')->willReturnSelf();
        $attributeMock->expects($this->once())->method('getEntityAttributeId')->willReturn(12);
        $attributeMock->expects($this->once())->method('getIsUserDefined')->willReturn(null);
        $attributeMock->expects($this->never())->method('deleteEntity');

        $this->model->unassign($attributeSetId, $attributeCode);
    }

    public function testGetAttributes()
    {
        $entityType = 'type';
        $attributeSetId = 148;

        $attributeSetMock = $this->getMock('Magento\Eav\Api\Data\AttributeSetInterface', [], [], '', false);
        $this->setRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->willReturn($attributeSetMock);
        $entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $this->eavConfigMock->expects($this->once())
            ->method('getEntityType')
            ->with($entityType)
            ->willReturn($entityTypeMock);
        $entityTypeMock->expects($this->once())->method('getId')->willReturn(88);
        $attributeSetMock->expects($this->exactly(2))->method('getId')->willReturn(88);
        $attributeSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(88);
        $this->attributeCollectionMock->expects($this->once())
            ->method('setAttributeSetFilter')
            ->with(88)
            ->willReturnSelf();
        $attributeMock = $this->getMock('Magento\Eav\Model\Entity\Attribute', [], [], '', false);
        $this->attributeCollectionMock->expects($this->once())->method('load')->willReturn([$attributeMock]);
        $attributeMock->expects($this->once())->method('getData')->willReturn(['data']);
        $this->attributeBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with(['data'])
            ->willReturnSelf();
        $this->attributeBuilderMock->expects($this->once())->method('create')->willReturn($attributeMock);

        $this->assertEquals([$attributeMock], $this->model->getAttributes($entityType, $attributeSetId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with attributeSetId = 148
     */
    public function testGetAttributesNoSuchEntityException()
    {
        $entityType = 'type';
        $attributeSetId = 148;

        $attributeSetMock = $this->getMock('Magento\Eav\Api\Data\AttributeSetInterface', [], [], '', false);
        $this->setRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->willReturn($attributeSetMock);
        $entityTypeMock = $this->getMock('Magento\Eav\Model\Entity\Type', [], [], '', false);
        $this->eavConfigMock->expects($this->once())
            ->method('getEntityType')
            ->with($entityType)
            ->willReturn($entityTypeMock);
        $entityTypeMock->expects($this->once())->method('getId')->willReturn(77);
        $attributeSetMock->expects($this->once())->method('getId')->willReturn(88);
        $attributeSetMock->expects($this->once())->method('getEntityTypeId')->willReturn(88);

        $this->model->getAttributes($entityType, $attributeSetId);
    }
}
