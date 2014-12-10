<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Model\Rma;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Rma\Service\V1\Data\Rma;

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
     * @var \Magento\Sales\Api\OrderRepositoryInterface
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
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param \Magento\Rma\Model\RmaRepository $rmaRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param Source\StatusFactory $statusFactory
     * @param RmaDataMapper $rmaDataMapper
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Rma\Model\RmaRepository $rmaRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        Source\StatusFactory $statusFactory,
        RmaDataMapper $rmaDataMapper,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->rmaFactory = $rmaFactory;
        $this->orderRepository = $orderRepository;
        $this->statusFactory = $statusFactory;
        $this->rmaDataMapper = $rmaDataMapper;
        $this->rmaRepository = $rmaRepository;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
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
            $items[$itemDto->getId()] = $this->extensibleDataObjectConverter->toFlatArray($itemDto);
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
