<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class TransactionReadTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sales\Service\V1\TransactionRead */
    protected $transactionRead;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Sales\Service\V1\Data\TransactionMapper|\PHPUnit_Framework_MockObject_MockObject */
    protected $transactionMapperMock;

    /** @var \Magento\Sales\Model\Order\Payment\TransactionRepository|\PHPUnit_Framework_MockObject_MockObject */
    protected $transactionRepositoryMock;

    /** @var \Magento\Sales\Service\V1\Data\TransactionSearchResultsBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $searchResultsBuilderMock;

    protected function setUp()
    {
        $this->transactionMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\TransactionMapper',
            [],
            [],
            '',
            false
        );
        $this->transactionRepositoryMock = $this->getMock(
            'Magento\Sales\Model\Order\Payment\TransactionRepository',
            ['get', 'find'],
            [],
            '',
            false
        );
        $this->searchResultsBuilderMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\TransactionSearchResultsBuilder',
            ['setItems', 'setTotalCount', 'setSearchCriteria', 'create'],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->transactionRead = $this->objectManagerHelper->getObject(
            'Magento\Sales\Service\V1\TransactionRead',
            [
                'transactionMapper' => $this->transactionMapperMock,
                'transactionRepository' => $this->transactionRepositoryMock,
                'searchResultsBuilder' => $this->searchResultsBuilderMock
            ]
        );
    }

    public function testGet()
    {
        $id = 1;
        $transaction = $this->getMock('Magento\Sales\Model\Order\Payment\Transaction', [], [], '', false);
        $transactionDto = $this->getMock('Magento\Sales\Service\V1\Data\Transaction', [], [], '', false);
        $this->transactionRepositoryMock->expects($this->once())
            ->method('get')
            ->with($id)
            ->will($this->returnValue($transaction));
        $this->transactionMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($transaction)
            ->will($this->returnValue($transactionDto));
        $this->assertEquals($transactionDto, $this->transactionRead->get($id));
    }

    public function testSearch()
    {
        /**
         * @var \Magento\Framework\Api\SearchCriteria $searchCriteria
         */
        $searchCriteria = $this->getMock('Magento\Framework\Api\SearchCriteria', [], [], '', false);
        $transactions = $this->getMock('Magento\Sales\Model\Order\Payment\Transaction', [], [], '', false);
        $transactionDto = $this->getMock('Magento\Sales\Service\V1\Data\Transaction', [], [], '', false);
        $searchResults = $this->getMock('Magento\Sales\Service\V1\Data\TransactionSearchResults', [], [], '', false);
        $this->transactionRepositoryMock->expects($this->once())
            ->method('find')
            ->with($searchCriteria)
            ->will($this->returnValue([$transactions]));
        $this->transactionMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($transactions, true)
            ->will($this->returnValue($transactionDto));
        $this->searchResultsBuilderMock->expects($this->once())
            ->method('setItems')
            ->with([$transactionDto])
            ->willReturnSelf();
        $this->searchResultsBuilderMock->expects($this->once())
            ->method('setTotalCount')
            ->with(1)
            ->willReturnSelf();
        $this->searchResultsBuilderMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteria)
            ->willReturnSelf();
        $this->searchResultsBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResults);
        $this->assertEquals($searchResults, $this->transactionRead->search($searchCriteria));
    }
}
