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
    private $labelService;

    /**
     * @var RmaRepository
     */
    private $rmaRepository;

    /**
     * @var \Magento\Rma\Model\Rma\Converter
     */
    private $converter;

    /**
     * @param \Magento\Rma\Model\Shipping\LabelService $labelService
     * @param RmaRepository $rmaRepository
     * @param \Magento\Rma\Model\Rma\Converter $converter
     */
    public function __construct(
        \Magento\Rma\Model\Shipping\LabelService $labelService,
        RmaRepository $rmaRepository,
        \Magento\Rma\Model\Rma\Converter $converter
    ) {
        $this->labelService = $labelService;
        $this->rmaRepository = $rmaRepository;
        $this->converter = $converter;
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
    /**
     * Create rma
     *
     * @param \Magento\Rma\Service\V1\Data\Rma $rmaDataObject
     * @return bool
     * @throws \Exception
     */
    public function create(\Magento\Rma\Service\V1\Data\Rma $rmaDataObject)
    {
        $preparedRmaData = $this->converter->getPreparedModelData($rmaDataObject);
        $rmaModel = $this->converter->createNewRmaModel($rmaDataObject, $preparedRmaData);
        return (bool)$rmaModel->saveRma($preparedRmaData);
    }

    /**
     * Update rma
     *
     * @param int $rmaId
     * @param \Magento\Rma\Service\V1\Data\Rma $rmaDataObject
     * @return bool
     * @throws \Exception
     */
    public function update($rmaId, \Magento\Rma\Service\V1\Data\Rma $rmaDataObject)
    {
        $preparedRmaData = $this->converter->getPreparedModelData($rmaDataObject);
        $rmaModel = $this->converter->getModel($rmaDataObject, $rmaId, $preparedRmaData);
        return (bool)$rmaModel->saveRma($preparedRmaData);
    }

    /**
     * Update rma
     *
     * @return bool
     * @throws \Exception
     */
    public function getRmaList()
    {
    }

    /**
     * Create shipping label for rma
     *
     * @param int $rmaId
     * @param \Magento\Rma\Service\V1\Data\Packages[] $packages
     * @param string $carrierCode
     * @param string $carrierTitle
     * @param string $methodTitle
     * @param null|float $price
     *
     * @throws \Exception
     * @return bool
     */
    public function createLabel($rmaId, $packages, $carrierCode = '', $carrierTitle = '', $methodTitle = '', $price = null)
    {
        $data = [
            'packages' => $packages,
            'code' => $carrierCode,
            'carrier_title' => $carrierTitle,
            'method_title' => $methodTitle,
            'price' => $price
        ];
        $rmaModel = $this->rmaRepository->get($rmaId);
        if ($rmaModel->getId()) {
            return (bool)$this->labelService->createShippingLabel($rmaModel, $data);
        }
        return false;
    }
}
