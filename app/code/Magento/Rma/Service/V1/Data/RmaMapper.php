<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

class RmaMapper
{
    /**
     * @var RmaBuilder
     */
    private $rmaBuilder;

    /**
     * @var ItemMapper
     */
    private $itemMapper;

    /**
     * @param RmaBuilder $rmaBuilder
     * @param ItemMapper $itemMapper
     */
    public function __construct(
        RmaBuilder $rmaBuilder,
        ItemMapper $itemMapper
    ) {
        $this->rmaBuilder = $rmaBuilder;
        $this->itemMapper = $itemMapper;
    }

    /**
     * Returns list of Item
     *
     * @param \Magento\Rma\Model\Resource\Item\Collection $items
     * @return array
     */
    public function getMappedItems(\Magento\Rma\Model\Resource\Item\Collection $items)
    {
        $dtoItems = [];
        foreach ($items as $itemModel) {
            $dtoItems[] = $this->itemMapper->extractDto($itemModel);
        }

        return $dtoItems;
    }

    /**
     * @param \Magento\Rma\Model\Rma $rmaModel
     * @return \Magento\Rma\Service\V1\Data\Rma
     */
    public function extractDto(\Magento\Rma\Model\Rma $rmaModel)
    {
        $this->rmaBuilder->populateWithArray($rmaModel->getData());
        $this->rmaBuilder->setItems($this->getMappedItems($rmaModel->getItemsForDisplay()));
        return $this->rmaBuilder->create();
    }
}
