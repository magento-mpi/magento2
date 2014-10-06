<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Service\V1\Data;

class WrappingRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\GiftWrapping\Model\WrappingRepository */
    protected $wrappingRepository;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $wrappingFactoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $collectionFactoryMock;

    protected function setUp()
    {
        $this->wrappingFactoryMock = $this->getMock(
            'Magento\GiftWrapping\Model\WrappingFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->collectionFactoryMock = $this->getMock(
            'Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->wrappingRepository = $this->objectManagerHelper->getObject(
            'Magento\GiftWrapping\Model\WrappingRepository',
            [
                'wrappingFactory' => $this->wrappingFactoryMock,
                'wrappingCollectionFactory' => $this->collectionFactoryMock
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetException()
    {
        list($id, $storeId) = [1, 1];
        /** @var \PHPUnit_Framework_MockObject_MockObject $wrappingMock */
        $wrappingMock = $this->getMock('Magento\GiftWrapping\Model\Wrapping', [], [], '', false);

        $this->wrappingFactoryMock->expects($this->once())->method('create')->will($this->returnValue($wrappingMock));
        $wrappingMock->expects($this->once())->method('setStoreId')->with($storeId);
        $wrappingMock->expects($this->once())->method('load')->with($id);
        $wrappingMock->expects($this->once())->method('getId')->will($this->returnValue(null));

        $this->wrappingRepository->get($id, $storeId);
    }

    public function testGetSuccess()
    {
        list($id, $storeId) = [1, 1];
        /** @var \PHPUnit_Framework_MockObject_MockObject $wrappingMock */
        $wrappingMock = $this->getMock('Magento\GiftWrapping\Model\Wrapping', [], [], '', false);

        $this->wrappingFactoryMock->expects($this->once())->method('create')->will($this->returnValue($wrappingMock));
        $wrappingMock->expects($this->once())->method('setStoreId')->with($storeId);
        $wrappingMock->expects($this->once())->method('load')->with($id);
        $wrappingMock->expects($this->once())->method('getId')->will($this->returnValue($id));

        $this->assertSame($wrappingMock, $this->wrappingRepository->get($id, $storeId));
    }

    public function testFindStatusFilter()
    {
        $criteriaMock = $this->preparedCriteriaFilterMock('status');
        list($collectionMock, $items) = $this->getPreparedCollectionAndItems();

        $collectionMock->expects($this->once())->method('applyStatusFilter');
        $this->assertSame($items, $this->wrappingRepository->find($criteriaMock));
    }

    public function testFindStoreIdFilter()
    {
        $criteriaMock = $this->preparedCriteriaFilterMock('store_id');
        list($collectionMock, $items) = $this->getPreparedCollectionAndItems();

        $collectionMock->expects($this->once())->method('addStoreAttributesToResult')->with(0);
        $this->assertSame($items, $this->wrappingRepository->find($criteriaMock));
    }

    /**
     * @param string|null $condition
     * @param string $expectedCondition
     * @dataProvider conditionDataProvider
     */
    public function testFindByCondition($condition, $expectedCondition)
    {
        $field = 'condition';
        $criteriaMock = $this->preparedCriteriaFilterMock($field, $condition);
        list($collectionMock, $items) = $this->getPreparedCollectionAndItems();

        $collectionMock->expects($this->once())->method('addFieldToFilter')->with(
            $field,
            [$expectedCondition => $field]
        );
        $this->assertSame($items, $this->wrappingRepository->find($criteriaMock));
    }

    /**
     * @return array
     */
    public function conditionDataProvider()
    {
        return [
            [null, 'eq'],
            ['not_eq', 'not_eq']
        ];
    }

    /**
     * Prepares mocks
     *
     * @param $filterType
     * @param string $condition
     * @return \Magento\Framework\Service\V1\Data\SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    private function preparedCriteriaFilterMock($filterType, $condition = 'eq')
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $criteriaMock */
        $criteriaMock = $this->getMock('Magento\Framework\Service\V1\Data\SearchCriteria', [], [], '', false);
        /** @var Data\Search\FilterGroup|\PHPUnit_Framework_MockObject_MockObject $filterGroup */
        $filterGroupMock = $this->getMock('Magento\Framework\Service\V1\Data\Search\FilterGroup', [], [], '', false);
        /** @var Data\Filter|\PHPUnit_Framework_MockObject_MockObject $filterMock */
        $filterMock = $this->getMock('Magento\Framework\Service\V1\Data\Filter', [], [], '', false);

        $criteriaMock->expects($this->once())->method('getFilterGroups')->will($this->returnValue([$filterGroupMock]));
        $filterGroupMock->expects($this->once())->method('getFilters')->will($this->returnValue([$filterMock]));

        $filterMock->expects($this->any())->method('getConditionType')->will($this->returnValue($condition));
        $filterMock->expects($this->any())->method('getField')->will($this->returnValue($filterType));
        $filterMock->expects($this->any())->method('getValue')->will($this->returnValue($filterType));

        return $criteriaMock;
    }

    /**
     * Prepares collection
     * @return array
     */
    private function getPreparedCollectionAndItems()
    {
        $items = [new \Magento\Framework\Object()];
        $collectionMock = $this->getMock('Magento\GiftWrapping\Model\Resource\Wrapping\Collection', [], [], '', false);

        $this->collectionFactoryMock->expects($this->once())->method('create')->will(
            $this->returnValue($collectionMock)
        );
        $collectionMock->expects($this->once())->method('addWebsitesToResult');
        $collectionMock->expects($this->once())->method('getItems')->will($this->returnValue($items));

        return [$collectionMock, $items];
    }
}
