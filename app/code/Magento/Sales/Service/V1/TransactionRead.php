<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Sales\Service\V1;

use Magento\Framework\Data\SearchCriteria;
use Magento\Sales\Model\Order\Payment\TransactionRepository;

class TransactionRead implements TransactionReadInterface
{
    /**
     * @var Data\TransactionMapper
     */
    private $transactionMapper;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var Data\TransactionSearchResultsBuilder
     */
    private $searchResultsBuilder;

    /**
     * @param Data\TransactionMapper $transactionMapper
     * @param TransactionRepository $transactionRepository
     * @param Data\TransactionSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        Data\TransactionMapper $transactionMapper,
        TransactionRepository $transactionRepository,
        Data\TransactionSearchResultsBuilder $searchResultsBuilder
    ) {
        $this->transactionMapper = $transactionMapper;
        $this->transactionRepository = $transactionRepository;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $transaction = $this->transactionRepository->get($id);
        return $this->transactionMapper->extractDto($transaction);
    }

    /**
     * {@inheritdoc}
     */
    public function search(SearchCriteria $searchCriteria)
    {
        $transactions = [];
        foreach ($this->transactionRepository->find($searchCriteria) as $transaction) {
            $transactions[] = $this->transactionMapper->extractDto($transaction, true);
        }
        return $this->searchResultsBuilder->setItems($transactions)
            ->setTotalCount(count($transactions))
            ->setSearchCriteria($searchCriteria)
            ->create();
    }
}
