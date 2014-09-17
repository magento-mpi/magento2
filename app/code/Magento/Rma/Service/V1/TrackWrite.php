<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

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
     * @param \Magento\Rma\Model\Shipping\LabelService $labelService
     * @param RmaRepository $rmaRepository
     */
    public function __construct(
        \Magento\Rma\Model\Shipping\LabelService $labelService,
        RmaRepository $rmaRepository
    ) {
        $this->labelService = $labelService;
        $this->rmaRepository = $rmaRepository;
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
        $rmaModel = $this->rmaRepository->get($id);
        if ($rmaModel->getId()) {
            return (bool)$this->labelService->addTrack(
                $rmaModel->getId(),
                $track->getTrackNumber(),
                $track->getCarrierCode(),
                $track->getCarrierTitle()
            );
        }
        return false;
    }

    /**
     * @param int $id
     * @param int $trackId
     *
     * @return bool
     */
    public function removeTrackById($id, $trackId)
    {
        $rmaModel = $this->rmaRepository->get($id);
        if ($rmaModel->getId()) {
            return (bool)$this->labelService->removeTrack($trackId);
        }
        return false;
    }
}
