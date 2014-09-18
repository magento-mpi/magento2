<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

use Magento\Rma\Model\Rma\PermissionChecker;

class TrackRead implements TrackReadInterface
{
    /**
     * @var \Magento\Rma\Model\RmaRepository
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
     * @param \Magento\Rma\Model\RmaRepository $repository
     * @param Data\TrackBuilder $trackBuilder
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        \Magento\Rma\Model\Shipping\LabelService $labelService,
        \Magento\Rma\Model\RmaRepository $repository,
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
        if ($rmaModel->getId()) {
            return base64_encode($this->labelService->getShippingLabelByRmaPdf($rmaModel));
        }
        return '';
    }
}
