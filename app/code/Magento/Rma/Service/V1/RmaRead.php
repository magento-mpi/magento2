<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

class RmaRead implements RmaReadInterface
{
    /**
     * @var \Magento\Rma\Model\Rma\Repository
     */
    private $repository;

    /**
     * @var Data\RmaMapper
     */
    private $rmaMapper;

    /**
     * @var Data\RmaSearchResultsBuilder
     */
    private $searchResultsBuilder;

    /**
     * @param \Magento\Rma\Model\Rma\Repository $repository
     * @param Data\RmaMapper $rmaMapper
     * @param Data\RmaSearchResultsBuilder $rmaSearchResultsBuilder
     */
    public function __construct(
        \Magento\Rma\Model\Rma\Repository $repository,
        Data\RmaMapper $rmaMapper,
        Data\RmaSearchResultsBuilder $rmaSearchResultsBuilder
    ) {
        $this->repository = $repository;
        $this->rmaMapper = $rmaMapper;
        $this->rmaSearchResultsBuilder = $rmaSearchResultsBuilder;
    }

    /**
     * Return data object for specified RMA id
     *
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\Rma
     */
    public function get($id)
    {
        $rmaModel = $this->repository->get($id);
        return $this->rmaMapper->extractDto($rmaModel);

    }

    /**
     * Return list of gift wrapping data objects based on search criteria
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Rma\Service\V1\Data\RmaSearchResults
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        $rmaList = [];
        foreach ($this->repository->find($searchCriteria) as $rmaModel) {
            $rmaList[] = $this->rmaMapper->extractDto($rmaModel);
        }
        return $this->rmaSearchResultsBuilder->setItems($rmaList)
            ->setTotalCount(count($rmaList))
            ->setSearchCriteria($searchCriteria)
            ->create();
    }
}
