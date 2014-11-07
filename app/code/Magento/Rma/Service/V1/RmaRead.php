<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

use Magento\Rma\Model\Rma\PermissionChecker;
use Magento\Rma\Model\RmaRepository;

class RmaRead implements RmaReadInterface
{
    /**
     * @var RmaRepository
     */
    private $repository;

    /**
     * @var Data\RmaMapper
     */
    private $rmaMapper;

    /**
     * @var Data\RmaSearchResultsBuilder
     */
    private $rmaSearchResultsBuilder;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @param RmaRepository $repository
     * @param Data\RmaMapper $rmaMapper
     * @param Data\RmaSearchResultsBuilder $rmaSearchResultsBuilder
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        RmaRepository $repository,
        Data\RmaMapper $rmaMapper,
        Data\RmaSearchResultsBuilder $rmaSearchResultsBuilder,
        PermissionChecker $permissionChecker
    ) {
        $this->repository = $repository;
        $this->rmaMapper = $rmaMapper;
        $this->rmaSearchResultsBuilder = $rmaSearchResultsBuilder;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * Return data object for specified RMA id
     *
     * @param int $id
     * @return Data\Rma
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkRmaForCustomerContext();
        $rmaModel = $this->repository->get($id);
        return $this->rmaMapper->extractDto($rmaModel);
    }

    /**
     * Return list of rma data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Rma\Service\V1\Data\RmaSearchResults
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkRmaForCustomerContext();
        $rmaList = [];
        foreach ($this->repository->find($searchCriteria) as $rmaModel) {
            if ($this->permissionChecker->isRmaOwner($rmaModel)) {
                $rmaList[] = $this->rmaMapper->extractDto($rmaModel);
            }
        }
        return $this->rmaSearchResultsBuilder->setItems($rmaList)
            ->setTotalCount(count($rmaList))
            ->setSearchCriteria($searchCriteria)
            ->create();
    }
}
