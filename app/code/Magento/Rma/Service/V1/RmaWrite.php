<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\Rma\Model\RmaRepository;

class RmaWrite implements RmaWriteInterface
{
    /**
     * @var \Magento\Rma\Model\Shipping\LabelService
     */
    protected $labelService;

    /**
     * @var RmaRepository
     */
    protected $rmaRepository;

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
     * @param int $rmaId
     * @param string $trackNumber
     * @param string $carrierCode
     * @param string $carrierTitle
     *
     * @throws \Exception
     * @return bool
     */
    public function addTrack($rmaId, $trackNumber, $carrierCode = '', $carrierTitle = '')
    {
        $rmaModel = $this->rmaRepository->get($rmaId);
        if ($rmaModel->getId()) {
            return (bool)$this->labelService->addTrack($rmaModel->getId(), $trackNumber, $carrierCode, $carrierTitle);
        }
        return false;
    }

    /**
     * @param int $rmaId
     * @param int $trackId
     *
     * @return bool
     */
    public function removeTrackById($rmaId, $trackId)
    {
        $rmaModel = $this->rmaRepository->get($rmaId);
        if ($rmaModel->getId()) {
            return (bool)$this->labelService->removeTrack($trackId);
        }
        return false;
    }
}
