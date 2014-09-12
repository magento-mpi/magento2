<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Model\Rma;

use Magento\Rma\Service\V1\Data\Rma;
use Magento\Framework\Service\ExtensibleDataObjectConverter;

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
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var Source\StatusFactory
     */
    private $statusFactory;

    /**
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param Source\StatusFactory $statusFactory
     * @param RmaDataMapper $rmaDataMapper
     */
    public function __construct(
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Rma\Model\Rma\Source\StatusFactory $statusFactory,
        \Magento\Rma\Model\Rma\RmaDataMapper $rmaDataMapper
    ) {
        $this->rmaFactory = $rmaFactory;
        $this->orderFactory = $orderFactory;
        $this->statusFactory = $statusFactory;
        $this->rmaDataMapper = $rmaDataMapper;
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
        $orderModel = $this->orderFactory->create();
        $orderModel->load($rmaDto->getOrderId());

        $rmaData = $this->rmaDataMapper->prepareNewRmaInstanceData($rmaData, $orderModel);

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
            $items[] = ExtensibleDataObjectConverter::toFlatArray($itemDto);
        }
        $rmaData['items'] = $items;

        $rmaData = $this->rmaDataMapper->filterRmaSaveRequest($rmaData);
        return $rmaData;
    }

    /**
     * Initiates and returns rma
     *
     * @param Rma $rmaDto
     * @param int $rmaId
     * @param array $preparedRmaData
     * @return \Magento\Rma\Model\Rma
     */
    public function getModel(Rma $rmaDto, $rmaId, array $preparedRmaData)
    {
        $rmaModel = $this->rmaFactory->create();
        $rmaModel->load($rmaId);

        $itemStatuses = $this->rmaDataMapper->combineItemStatuses($preparedRmaData['items'], $rmaDto->getEntityId());

        $sourceStatus = $this->statusFactory->create();
        $rmaModel->setStatus($sourceStatus->getStatusByItems($itemStatuses))->setIsUpdate(1);

        return $rmaModel;
    }
}
