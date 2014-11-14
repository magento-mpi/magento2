<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute;

class SetRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\SetRepository
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    protected function setUp()
    {
        $this->attrSetRepositoryMock = $this->getMock('\Magento\Eav\Api\AttributeSetRepositoryInterface');
        $this->searchCriteriaBuilderMock = $this->getMock(
            '\Magento\Framework\Api\SearchCriteriaDataBuilder',
            [],
            [],
            '',
            false
        );
        $this->filterBuilderMock = $this->getMock(
            '\Magento\Framework\Api\FilterBuilder',
            [],
            [],
            '',
            false
        );
        $this->model = new \Magento\Catalog\Model\Product\Attribute\SetRepository(
            $this->attrSetRepositoryMock,
            $this->searchCriteriaBuilderMock,
            $this->filterBuilderMock
        );
    }

    public function testSave()
    {
        $attributeSetMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $this->attrSetRepositoryMock->expects($this->once())
            ->method('save')
            ->with($attributeSetMock)
            ->willReturn($attributeSetMock);
        $this->assertEquals($attributeSetMock, $this->model->save($attributeSetMock));
    }

    public function testGet()
    {
        $attributeSetId = 1;
        $attributeSetMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $this->attrSetRepositoryMock->expects($this->once())
            ->method('get')
            ->with($attributeSetId)
            ->willReturn($attributeSetMock);
        $this->assertEquals($attributeSetMock, $this->model->get($attributeSetId));
    }

    public function testDelete()
    {
        $attributeSetMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetInterface');
        $this->attrSetRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($attributeSetMock)
            ->willReturn(true);
        $this->assertTrue($this->model->delete($attributeSetMock));
    }

    public function testDeleteById()
    {
        $attributeSetId = 1;
        $this->attrSetRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($attributeSetId)
            ->willReturn(true);
        $this->assertTrue($this->model->deleteById($attributeSetId));
    }

    public function testGetList()
    {
        $searchResultMock = $this->getMock('\Magento\Eav\Api\Data\AttributeSetSearchResultsInterface');

        $searchCriteriaMock = $this->getMock('\Magento\Framework\Api\SearchCriteriaInterface');
        $searchCriteriaMock->expects($this->once())->method('getCurrentPage')->willReturn(1);
        $searchCriteriaMock->expects($this->once())->method('getPageSize')->willReturn(2);

        $filterMock = $this->getMock('\Magento\Framework\Api\Filter', [], [], '', false);

        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with('entity_type_code')
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())
            ->method('setConditionType')
            ->with('eq')
            ->willReturnSelf();
        $this->filterBuilderMock->expects($this->once())->method('create')->willReturn($filterMock);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with([$filterMock])
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setCurrentPage')
            ->with(1)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setPageSize')
            ->with(2)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->getMock('\Magento\Framework\Api\SearchCriteriaInterface'));

        $this->attrSetRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);
        $this->assertEquals($searchResultMock, $this->model->getList($searchCriteriaMock));
    }
}
