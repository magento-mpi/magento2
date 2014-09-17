<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

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
     * @param \Magento\Rma\Model\Shipping\LabelService $labelService
     * @param \Magento\Rma\Model\RmaRepository $repository
     * @param Data\TrackBuilder $trackBuilder
     */
    public function __construct(
        \Magento\Rma\Model\Shipping\LabelService $labelService,
        \Magento\Rma\Model\RmaRepository $repository,
        Data\TrackBuilder $trackBuilder
    ) {
        $this->repository = $repository;
        $this->trackBuilder = $trackBuilder;
        $this->labelService = $labelService;
    }

    /**
     * Return list of track data objects based on search criteria
     *
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\Track[]
     */
    public function getTracks($id)
    {
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
        $rmaModel = $this->repository->get($id);
        if ($rmaModel->getId()) {
            return $this->labelService->getShippingLabelByRmaPdf($rmaModel);
        }
        return '';
    }
}
