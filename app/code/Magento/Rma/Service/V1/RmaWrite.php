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
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\Rma $rmaDataObject
     * @return bool
     * @throws \Exception
     */
    public function update($id, \Magento\Rma\Service\V1\Data\Rma $rmaDataObject)
    {
        $preparedRmaData = $this->converter->getPreparedModelData($rmaDataObject);
        $rmaModel = $this->converter->getModel($id, $preparedRmaData);
        return (bool)$rmaModel->saveRma($preparedRmaData);
    }

    /**
     * Create shipping label for rma
     *
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\Packages[] $packages
     * @param string $carrierCode
     * @param string $carrierTitle
     * @param string $methodTitle
     * @param null|float $price
     *
     * @throws \Exception
     * @return bool
     */
    public function createLabel($id, $packages, $carrierCode = '', $carrierTitle = '', $methodTitle = '', $price = null)
    {
        $data = [
            'packages' => $packages,
            'code' => $carrierCode,
            'carrier_title' => $carrierTitle,
            'method_title' => $methodTitle,
            'price' => $price
        ];
        $rmaModel = $this->rmaRepository->get($id);
        if ($rmaModel->getId()) {
            return (bool)$this->labelService->createShippingLabel($rmaModel, $data);
        }
        return false;
    }
}
