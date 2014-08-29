<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Order\Payment;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class TransactionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sales\Model\Order\Payment\TransactionRepository */
    protected $transactionRepository;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Sales\Model\Order\Payment\TransactionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Payment\Transaction\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilder;

    protected function setUp()
    {
        $this->transactionFactory = $this->getMock(
            'Magento\Sales\Model\Order\Payment\TransactionFactory',
            [],
            [],
            '',
            false
        );
        $this->transactionCollectionFactory = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Payment\Transaction\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->filterBuilder = $this->getMock('Magento\Framework\Service\V1\Data\FilterBuilder', [], [], '', false);
        $this->searchCriteriaBuilder = $this->getMock(
            'Magento\Framework\Service\V1\Data\SearchCriteriaBuilder',
            [],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->transactionRepository = $this->objectManagerHelper->getObject(
            'Magento\Sales\Model\Order\Payment\TransactionRepository',
            [
                'transactionFactory' => $this->transactionFactory,
                'transactionCollectionFactory' => $this->transactionCollectionFactory,
                'filterBuilder' => $this->filterBuilder,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilder
            ]
        );
    }

    public function testGetIOException()
    {
        $this->setExpectedException('Magento\Framework\Exception\InputException', 'ID required');
        $this->transactionRepository->get(null);
    }

    /**
     * @dataProvider getDataProvider
     */
    public function testGet($id, $conditionType)
    {
        $filter = $this->getMock(
            'Magento\Framework\Service\V1\Data\Filter',
            ['getConditionType', 'getField', 'getValue'],
            [],
            '',
            false
        );
        $filter->expects($this->any())->method('getConditionType')->willReturn($conditionType);

        $this->filterBuilder->expects($this->once())->method('setField')->with('transaction_id')->willReturnSelf();
        $this->filterBuilder->expects($this->once())->method('setValue')->with($id)->willReturnSelf();
        $this->filterBuilder->expects($this->once())->method('setConditionType')->with('eq')->willReturnSelf();
        $this->filterBuilder->expects($this->once())->method('create')->willReturn($filter);

        $filterGroup = $this->getMock('Magento\Framework\Service\V1\Data\Search\FilterGroup', [], [], '', false);
        $filterGroup->expects($this->any())
            ->method('getFilters')
            ->willReturn($filter);
        $searchCriteria = $this->getMock('Magento\Framework\Service\V1\Data\SearchCriteria', [], [], '', false);
        $searchCriteria->expects($this->any())
            ->method('getFilterGroups')
            ->willReturn([$filterGroup]);
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('addFilter')
            ->with([$filter]);
        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $collection = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Payment\Transaction\Collection',
            [
                'addFieldToFilter', 'setCurPage', 'setPageSize', 'addPaymentInformation',
                'addOrderInformation', 'getAllIds', 'dispatch'
            ],
            [],
            '',
            false
        );
        $this->transactionCollectionFactory->expects($this->once())
            ->method('create')
            ->willReturn($collection);

        $this->transactionRepository->get($id);
    }

    /**
     * @return array
     */
    public function getDataProvider()
    {
        return [
            [1, 'eq'],
            [1, null],
        ];
    }
}
