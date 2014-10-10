<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

use Magento\Rma\Model\RmaRepository;
use Magento\Rma\Model\Rma\PermissionChecker;
use Magento\Framework\Exception\StateException;

class TrackWrite implements TrackWriteInterface
{
    /**
     * @var \Magento\Rma\Model\Shipping\LabelService
     */
    private $labelService;

    /**
     * @var RmaRepository
     */
    private $rmaRepository;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @param \Magento\Rma\Model\Shipping\LabelService $labelService
     * @param RmaRepository $rmaRepository
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        \Magento\Rma\Model\Shipping\LabelService $labelService,
        RmaRepository $rmaRepository,
        PermissionChecker $permissionChecker
    ) {
        $this->labelService = $labelService;
        $this->rmaRepository = $rmaRepository;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\Track $track
     *
     * @throws \Exception
     * @return bool
     */
    public function addTrack($id, \Magento\Rma\Service\V1\Data\Track $track)
    {
        if ($this->permissionChecker->isCustomerContext()) {
            throw new StateException('Unknown service');
        }

        /** @var  $rmaModel */
        $rmaModel = $this->rmaRepository->get($id);
        return (bool)$this->labelService->addTrack(
            $rmaModel->getId(),
            $track->getTrackNumber(),
            $track->getCarrierCode(),
            $track->getCarrierTitle()
        );
    }

    /**
     * @param int $id
     * @param int $trackId
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function removeTrackById($id, $trackId)
    {
        if ($this->permissionChecker->isCustomerContext()) {
            throw new StateException('Unknown service');
        }

        $rmaModel = $this->rmaRepository->get($id);
        return (bool)$this->labelService->removeTrack($trackId, $rmaModel->getId());
    }
}
