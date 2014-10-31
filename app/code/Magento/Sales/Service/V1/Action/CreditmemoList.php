<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\CreditmemoMapper;
use Magento\Sales\Model\Order\CreditmemoRepository;
use Magento\Framework\Data\SearchCriteria;
use Magento\Sales\Service\V1\Data\CreditmemoSearchResultsBuilder;

/**
 * Class CreditmemoList
 */
class CreditmemoList
{
    /**
     * @var CreditmemoMapper
     */
    protected $creditmemoMapper;

    /**
     * @var CreditmemoRepository
     */
    protected $creditmemoRepository;

    /**
     * @var CreditmemoSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param CreditmemoRepository $creditmemoRepository
     * @param CreditmemoMapper $creditmemoMapper
     * @param CreditmemoSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        CreditmemoRepository $creditmemoRepository,
        CreditmemoMapper $creditmemoMapper,
        CreditmemoSearchResultsBuilder $searchResultsBuilder
    ) {
        $this->creditmemoRepository = $creditmemoRepository;
        $this->creditmemoMapper = $creditmemoMapper;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * Invoke CreditmemoList service
     *
     * @param \Magento\Framework\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Service\V1\Data\SearchResults
     */
    public function invoke(SearchCriteria $searchCriteria)
    {
        $creditmemos = [];
        foreach ($this->creditmemoRepository->find($searchCriteria) as $creditmemo) {
            $creditmemos[] = $this->creditmemoMapper->extractDto($creditmemo);
        }

        return $this->searchResultsBuilder->setItems($creditmemos)
            ->setTotalCount(count($creditmemos))
            ->setSearchCriteria($searchCriteria)
            ->create();
    }
}
