<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class GroupRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Attribute\GroupRepository
     */
    protected $model;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupResourceMock;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\GroupFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupFactoryMock;

    /**
     * @var \Magento\Eav\Api\Data\AttributeGroupInterfaceDataBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupBuilderMock;

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $setRepositoryMock;

    /**
     * @var \Magento\Framework\Data\Search\SearchResultsInterfaceBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultsBuilderMock;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory|
     *      \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupListFactoryMock;

    protected function setUp()
    {
        $this->groupResourceMock = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Group',
            [],
            [],
            '',
            false
        );
        $this->groupFactoryMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\GroupFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->groupBuilderMock = $this->getMock(
            '\Magento\Eav\Api\Data\AttributeGroupInterfaceDataBuilder',
            ['setId', 'setName', 'setAttributeSetId', 'create']
        );
        $this->setRepositoryMock = $this->getMock('\Magento\Eav\Api\AttributeSetRepositoryInterface');
        $this->searchResultsBuilderMock = $this->getMock(
            '\Magento\Framework\Data\Search\SearchResultsInterfaceBuilder',
            ['setSearchCriteria', 'setItems', 'setTotalCount', 'create']
        );
        $this->groupListFactoryMock = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->model = new \Magento\Eav\Model\Attribute\GroupRepository(
            $this->groupResourceMock,
            $this->groupListFactoryMock,
            $this->groupFactoryMock,
            $this->groupBuilderMock,
            $this->setRepositoryMock,
            $this->searchResultsBuilderMock
        );
    }

    public function testSave()
    {
        $attributeSetId = 42;
        $groupId = 20;
        $groupMock = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $groupDataMock = $this->getMock('\Magento\Framework\Model\AbstractModel', [], [], '', false);
        $existingGroupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $attributeSetInterfaceMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $groupMock->expects($this->any())->method('getAttributeSetId')->willReturn($attributeSetId);
        $groupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $attributeSetInterfaceMock->expects($this->any())->method('getId')->willReturn(10);
        $this->setRepositoryMock->expects($this->once())->method('get')->with($attributeSetId)
            ->willReturn($attributeSetInterfaceMock);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($existingGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($existingGroupMock, $groupId);
        $existingGroupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $existingGroupMock->expects($this->once())->method('getAttributeSetId')->willReturn($attributeSetId);
        $this->groupBuilderMock->expects($this->once())->method('setId')->with($groupId);
        $this->groupBuilderMock->expects($this->once())->method('create')->willReturn($groupDataMock);
        $this->groupResourceMock->expects($this->once())->method('save')->with($groupDataMock);
        $this->assertEquals($groupDataMock, $this->model->save($groupMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Cannot save attributeGroup
     */
    public function testSaveWithStateException()
    {
        $attributeSetId = 42;
        $groupId = 20;
        $groupMock = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $existingGroupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $attributeSetInterfaceMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $groupMock->expects($this->any())->method('getAttributeSetId')->willReturn($attributeSetId);
        $groupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $attributeSetInterfaceMock->expects($this->any())->method('getId')->willReturn(10);
        $this->setRepositoryMock->expects($this->once())->method('get')->with($attributeSetId)
            ->willReturn($attributeSetInterfaceMock);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($existingGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($existingGroupMock, $groupId);
        $existingGroupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $existingGroupMock->expects($this->once())->method('getAttributeSetId')->willReturn($attributeSetId);
        $this->groupBuilderMock->expects($this->once())->method('setId')->with($groupId);
        $this->model->save($groupMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Attribute group does not belong to provided attribute set
     */
    public function testSaveWithAttributeGroupException()
    {
        $attributeSetId = 42;
        $groupId = 20;
        $groupMock = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $existingGroupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $attributeSetInterfaceMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $groupMock->expects($this->any())->method('getAttributeSetId')->willReturn($attributeSetId);
        $groupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $attributeSetInterfaceMock->expects($this->any())->method('getId')->willReturn(10);
        $this->setRepositoryMock->expects($this->once())->method('get')->with($attributeSetId)
            ->willReturn($attributeSetInterfaceMock);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($existingGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($existingGroupMock, $groupId);
        $existingGroupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $this->model->save($groupMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with attributeGroupId =
     */
    public function testSaveWithNoIdException()
    {
        $attributeSetId = 42;
        $groupId = 20;
        $groupMock = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $existingGroupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $attributeSetInterfaceMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $groupMock->expects($this->any())->method('getAttributeSetId')->willReturn($attributeSetId);
        $groupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $attributeSetInterfaceMock->expects($this->any())->method('getId')->willReturn(10);
        $this->setRepositoryMock->expects($this->once())->method('get')->with($attributeSetId)
            ->willReturn($attributeSetInterfaceMock);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($existingGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($existingGroupMock, $groupId);
        $existingGroupMock->expects($this->any())->method('getId')->willReturn(false);
        $this->model->save($groupMock);
    }

    public function testGetList()
    {
        $attributeSetId = 'filter';
        $searchCriteriaMock = $this->getMock('\Magento\Framework\Data\Search\SearchCriteriaInterface');
        $filterGroupInterfaceMock = $this->getMock('\Magento\Framework\Data\Search\FilterGroupInterface');
        $filterInterfaceMock = $this->getMock('\Magento\Framework\Data\Search\FilterInterface');
        $attributeSetInterfaceMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $groupMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Group',
            ['getAttributeGroupName', 'getId'],
            [],
            '',
            false
        );
        $groupMock->expects($this->once())->method('getId')->willReturn('10');
        $groupMock->expects($this->once())->method('getAttributeGroupName')->willReturn('attributeGroup');
        $groupCollectionMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Collection\AbstractCollection',
            ['setAttributeSetFilter', 'setSortOrder', 'getItems', 'getSize'],
            [],
            '',
            false,
            false,
            false
        );
        $groupCollectionMock->expects($this->once())->method('getItems')->willReturn([$groupMock]);
        $searchCriteriaMock->expects($this->once())->method('getFilterGroups')->willReturn([$filterGroupInterfaceMock]);
        $filterGroupInterfaceMock->expects($this->once())->method('getFilters')->willReturn([$filterInterfaceMock]);
        $filterInterfaceMock->expects($this->once())->method('getField')->willReturn('attribute_set_id');
        $filterInterfaceMock->expects($this->once())->method('getValue')->willReturn($attributeSetId);
        $this->setRepositoryMock->expects($this->once())->method('get')->with($attributeSetId)
            ->willReturn($attributeSetInterfaceMock);
        $attributeSetInterfaceMock->expects($this->any())->method('getId')->willReturn(10);
        $this->groupListFactoryMock->expects($this->once())->method('create')->willReturn($groupCollectionMock);
        $groupCollectionMock->expects($this->once())->method('setAttributeSetFilter')->with($attributeSetId);
        $groupCollectionMock->expects($this->once())->method('setSortOrder');
        $groupCollectionMock->expects($this->once())->method('getSize')->willReturn(1);
        $this->groupBuilderMock->expects($this->once())->method('setId')->with('10');
        $this->groupBuilderMock->expects($this->once())->method('setName')->with('attributeGroup');
        $this->groupBuilderMock->expects($this->once())->method('create')->willReturn('groups');
        $this->searchResultsBuilderMock->expects($this->once())->method('setSearchCriteria')->with($searchCriteriaMock);
        $this->searchResultsBuilderMock->expects($this->once())->method('setItems')->with(['groups']);
        $this->searchResultsBuilderMock->expects($this->once())->method('setTotalCount')->with(1);
        $this->searchResultsBuilderMock->expects($this->once())->method('create')->willReturnSelf();
        $this->assertEquals($this->searchResultsBuilderMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage attribute_set_id is a required field.
     */
    public function testGetListWithInvalidInputException()
    {
        $searchCriteriaMock = $this->getMock('\Magento\Framework\Data\Search\SearchCriteriaInterface');
        $searchCriteriaMock->expects($this->once())->method('getFilterGroups')->willReturn([]);
        $this->model->getList($searchCriteriaMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with attributeSetId = filter
     */
    public function testGetListWithNoSuchEntityException()
    {
        $attributeSetId = 'filter';
        $searchCriteriaMock = $this->getMock('\Magento\Framework\Data\Search\SearchCriteriaInterface');
        $filterGroupInterfaceMock = $this->getMock('\Magento\Framework\Data\Search\FilterGroupInterface');
        $filterInterfaceMock = $this->getMock('\Magento\Framework\Data\Search\FilterInterface');
        $attributeSetInterfaceMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $searchCriteriaMock->expects($this->once())->method('getFilterGroups')->willReturn([$filterGroupInterfaceMock]);
        $filterGroupInterfaceMock->expects($this->once())->method('getFilters')->willReturn([$filterInterfaceMock]);
        $filterInterfaceMock->expects($this->once())->method('getField')->willReturn('attribute_set_id');
        $filterInterfaceMock->expects($this->once())->method('getValue')->willReturn($attributeSetId);
        $searchCriteriaMock->expects($this->once())->method('getFilterGroups')->willReturn([]);
        $this->setRepositoryMock->expects($this->once())->method('get')->with($attributeSetId)
            ->willReturn($attributeSetInterfaceMock);
        $attributeSetInterfaceMock->expects($this->any())->method('getId')->willReturn(false);
        $this->model->getList($searchCriteriaMock);
    }

    public function testGet()
    {
        $groupId = 42;
        $groupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($groupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($groupMock, $groupId);
        $groupMock->expects($this->once())->method('getId')->willReturn($groupId);
        $this->assertEquals($groupMock, $this->model->get($groupId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Group with id "42" does not exist.
     */
    public function testGetWithException()
    {
        $groupId = 42;
        $groupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($groupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($groupMock, $groupId);
        $groupMock->expects($this->once())->method('getId')->willReturn(false);
        $this->assertEquals($groupMock, $this->model->get($groupId));
    }

    public function testDelete()
    {
        $groupId = 42;
        $groupAttributeSetId = 20;
        $attributeGroupId = 30;
        $attributeGroupSetId = 20;
        $attributeGroupMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            ['getAttributeSetId', 'getId'],
            [],
            '',
            false
        );
        $attributeGroupMock->expects($this->once())->method('getId')->willReturn($attributeGroupId);
        $attributeGroupMock->expects($this->once())->method('getAttributeSetId')->willReturn($attributeGroupSetId);
        $groupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $groupMock->expects($this->once())->method('getId')->willReturn($groupId);
        $groupMock->expects($this->once())->method('getAttributeSetId')->willReturn($groupAttributeSetId);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($attributeGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($attributeGroupMock, $groupId);
        $this->groupResourceMock->expects($this->once())->method('delete')->with($attributeGroupMock);
        $this->assertTrue($this->model->delete($groupMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Cannot delete attributeGroup with id 42
     */
    public function testDeleteWithCanNotDeleteException()
    {
        $groupId = 42;
        $groupAttributeSetId = 20;
        $attributeGroupId = 30;
        $attributeGroupSetId = 20;
        $attributeGroupMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            ['getAttributeSetId', 'getId'],
            [],
            '',
            false
        );
        $attributeGroupMock->expects($this->once())->method('getId')->willReturn($attributeGroupId);
        $attributeGroupMock->expects($this->once())->method('getAttributeSetId')->willReturn($attributeGroupSetId);
        $groupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $groupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $groupMock->expects($this->once())->method('getAttributeSetId')->willReturn($groupAttributeSetId);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($attributeGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($attributeGroupMock, $groupId);
        $this->groupResourceMock->expects($this->once())->method('delete')->with($attributeGroupMock)
            ->willThrowException(new \Exception('Something went wrong.'));
        $this->model->delete($groupMock);
    }

    /**
     * @dataProvider deleteWithExceptionDataProvider
     */
    public function testDeleteWithException($groupId, $groupAttributeSetId, $attributeGroupId, $attributeGroupSetId, $e)
    {
        $this->setExpectedException($e['name'], $e['message']);
        $attributeGroupMock = $this->getMock(
            '\Magento\Framework\Model\AbstractModel',
            ['getAttributeSetId', 'getId'],
            [],
            '',
            false
        );
        $attributeGroupMock->expects($this->once())->method('getId')->willReturn($attributeGroupId);
        $attributeGroupMock->expects($this->any())->method('getAttributeSetId')->willReturn($attributeGroupSetId);
        $groupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $groupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $groupMock->expects($this->any())->method('getAttributeSetId')->willReturn($groupAttributeSetId);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($attributeGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($attributeGroupMock, $groupId);
        $this->groupResourceMock->expects($this->never())->method('delete');
        $this->model->delete($groupMock);
    }

    public function deleteWithExceptionDataProvider()
    {
        return [
            [42, null, null, null, [
                    'name' => '\Magento\Framework\Exception\NoSuchEntityException',
                    'message' => 'No such entity with attributeGroupId = 42'
                ]
            ],
            [42, 12, 24, 6, [
                    'name' => '\Magento\Framework\Exception\StateException',
                    'message' => 'Attribute group does not belong to provided attribute set'
                ]
            ]
        ];
    }

    public function testDeleteById()
    {
        $groupId = 42;
        $groupMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Group', [], [], '', false);
        $this->groupFactoryMock->expects($this->any())->method('create')->willReturn($groupMock);
        $this->groupResourceMock->expects($this->any())->method('load')->with($groupMock, $groupId);
        $groupMock->expects($this->any())->method('getId')->willReturn($groupId);
        $this->assertTrue($this->model->deleteById($groupId));
    }
}
