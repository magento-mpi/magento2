<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class ProductAttributeGroupRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\ProductAttributeGroupRepository
     */
    protected $model;

    /**
     * @var \Magento\Eav\Api\AttributeGroupRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Group|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupFactoryMock;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupResourceMock;

    protected function setUp()
    {
        $this->groupRepositoryMock = $this->getMock('\Magento\Eav\Api\AttributeGroupRepositoryInterface');
        $this->groupFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\Product\Attribute\GroupFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->groupResourceMock = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Group',
            [],
            [],
            '',
            false
        );
        $this->model = new \Magento\Catalog\Model\ProductAttributeGroupRepository(
            $this->groupRepositoryMock,
            $this->groupResourceMock,
            $this->groupFactoryMock
        );
    }

    public function testSave()
    {
        $groupMock = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $expectedResult = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $this->groupRepositoryMock->expects($this->once())->method('save')->with($groupMock)
            ->willReturn($expectedResult);
        $this->assertEquals($expectedResult, $this->model->save($groupMock));
    }

    public function testGetList()
    {
        $serchCriteriaMock = $this->getMock('\Magento\Framework\Data\Search\SearchCriteriaInterface');
        $expectedResult = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $this->groupRepositoryMock->expects($this->once())->method('getList')->with($serchCriteriaMock)
            ->willReturn($expectedResult);
        $this->assertEquals($expectedResult, $this->model->getList($serchCriteriaMock));
    }

    public function testGet()
    {
        $groupId = 42;
        $groupMock = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $this->groupRepositoryMock->expects($this->once())->method('get')->with($groupId)->willReturn($groupMock);
        $this->assertEquals($groupMock, $this->model->get($groupId));
    }

    public function testDeleteById()
    {
        $groupId = 42;
        $groupMock = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $this->groupRepositoryMock->expects($this->once())->method('get')->with($groupId)->willReturn($groupMock);
        $groupMock->expects($this->once())->method('getId')->willReturn($groupId);
        $attributeGroupMock = $this->getMock('\Magento\Catalog\Model\Product\Attribute\Group', [], [], '', false);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($attributeGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($attributeGroupMock, $groupId);
        $attributeGroupMock->expects($this->once())->method('hasSystemAttributes')->willReturn(false);
        $this->groupRepositoryMock->expects($this->once())->method('delete')->willReturn(true);
        $this->assertTrue($this->model->deleteById($groupId));
    }

    public function testDelete()
    {
        $groupId = 42;
        $groupMock = $this->getMock('\Magento\Eav\Api\Data\AttributeGroupInterface');
        $groupMock->expects($this->once())->method('getId')->willReturn($groupId);
        $attributeGroupMock = $this->getMock('\Magento\Catalog\Model\Product\Attribute\Group', [], [], '', false);
        $this->groupFactoryMock->expects($this->once())->method('create')->willReturn($attributeGroupMock);
        $this->groupResourceMock->expects($this->once())->method('load')->with($attributeGroupMock, $groupId);
        $attributeGroupMock->expects($this->once())->method('hasSystemAttributes')->willReturn(false);
        $this->groupRepositoryMock->expects($this->once())->method('delete')->willReturn(true);
        $this->assertTrue($this->model->delete($groupMock));
    }
}
