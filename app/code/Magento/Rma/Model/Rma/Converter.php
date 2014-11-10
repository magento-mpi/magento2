<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Model\Rma;

use Magento\Rma\Service\V1\Data\Rma;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class Converter
{
    /**
     * @var \Magento\Rma\Model\RmaFactory
     */
    private $rmaFactory;

    /**
     * @var \Magento\Rma\Model\Rma\RmaDataMapper
     */
    private $rmaDataMapper;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @var Source\StatusFactory
     */
    private $statusFactory;

    /**
     * @var RmaRepository
     */
    private $rmaRepository;

    /**
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param \Magento\Rma\Model\RmaRepository $rmaRepository
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param Source\StatusFactory $statusFactory
     * @param RmaDataMapper $rmaDataMapper
     */
    public function __construct(
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Rma\Model\RmaRepository $rmaRepository,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        Source\StatusFactory $statusFactory,
        RmaDataMapper $rmaDataMapper
    ) {
        $this->rmaFactory = $rmaFactory;
        $this->orderRepository = $orderRepository;
        $this->statusFactory = $statusFactory;
        $this->rmaDataMapper = $rmaDataMapper;
        $this->rmaRepository = $rmaRepository;
    }

    /**
     * Creates rma model for new entity
     *
     * @param Rma $rmaDto
     * @param array $rmaData
     * @return \Magento\Rma\Model\Rma
     */
    public function createNewRmaModel(Rma $rmaDto, array $rmaData)
    {
        $orderModel = $this->orderRepository->get($rmaDto->getOrderId());

        $rmaData = $this->rmaDataMapper->filterRmaSaveRequest($rmaData);

        $rmaModel = $this->rmaFactory->create();
        $rmaModel->setData($this->rmaDataMapper->prepareNewRmaInstanceData($rmaData, $orderModel));

        return $rmaModel;
    }

    /**
     * Prepares Rma data
     *
     * @param Rma $rmaDto
     * @return array
     */
    public function getPreparedModelData(Rma $rmaDto)
    {
        $rmaData = $rmaDto->__toArray();
        $items = [];
        foreach ($rmaDto->getItems() as $itemDto) {
            $items[$itemDto->getId()] = ExtensibleDataObjectConverter::toFlatArray($itemDto);
        }
        $rmaData['items'] = $items;

        $rmaData = $this->rmaDataMapper->filterRmaSaveRequest($rmaData);
        return $rmaData;
    }

    /**
     * Initiates and returns rma
     *
     * @param int $rmaId
     * @param array $preparedRmaData
     * @return \Magento\Rma\Model\Rma
     */
    public function getModel($rmaId, array $preparedRmaData)
    {
        $rmaModel = $this->rmaRepository->get($rmaId);

        $itemStatuses = $this->rmaDataMapper->combineItemStatuses($preparedRmaData['items'], $rmaId);

        $sourceStatus = $this->statusFactory->create();
        $rmaModel->setStatus($sourceStatus->getStatusByItems($itemStatuses));
        $rmaModel->setIsUpdate(1);

        return $rmaModel;
    }
}
