<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Service\V1;

use Magento\Rma\Model\Rma\PermissionChecker;
use Magento\Rma\Model\RmaRepository;

class TrackRead implements TrackReadInterface
{
    /**
     * @var RmaRepository
     */
    private $repository;

    /**
     * @var Data\TrackBuilder
     */
    private $trackBuilder;

    /**
     * @var \Magento\Rma\Model\Shipping\LabelService
     */
    private $labelService;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @param \Magento\Rma\Model\Shipping\LabelService $labelService
     * @param RmaRepository $repository
     * @param Data\TrackBuilder $trackBuilder
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        \Magento\Rma\Model\Shipping\LabelService $labelService,
        RmaRepository $repository,
        Data\TrackBuilder $trackBuilder,
        PermissionChecker $permissionChecker
    ) {
        $this->repository = $repository;
        $this->trackBuilder = $trackBuilder;
        $this->labelService = $labelService;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * Return list of track data objects based on search criteria
     *
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\Track[]
     */
    public function getTracks($id)
    {
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkRmaForCustomerContext();

        $rmaModel = $this->repository->get($id);
        $tracks = [];
        foreach ($rmaModel->getTrackingNumbers() as $track) {
            $this->trackBuilder->populateWithArray($track->getData());
            $tracks[] = $this->trackBuilder->create();
        }
        return $tracks;
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     * @return string
     */
    public function getShippingLabelPdf($id)
    {
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkRmaForCustomerContext();

        $rmaModel = $this->repository->get($id);

        return base64_encode($this->labelService->getShippingLabelByRmaPdf($rmaModel));
    }
}
