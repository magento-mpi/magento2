<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1\Data;

use Magento\Rma\Service\V1\CommentReadInterface;
use Magento\Rma\Service\V1\TrackReadInterface;

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
     * @var \Magento\Rma\Service\V1\TrackReadInterface
     */
    private $trackReadService;

    /**
     * @var \Magento\Rma\Service\V1\CommentReadInterface
     */
    private $commentReadService;

    /**
     * @param RmaBuilder $rmaBuilder
     * @param ItemMapper $itemMapper
     * @param TrackReadInterface $trackReadService
     * @param CommentReadInterface $commentReadService
     */
    public function __construct(
        RmaBuilder $rmaBuilder,
        ItemMapper $itemMapper,
        TrackReadInterface $trackReadService,
        CommentReadInterface $commentReadService
    ) {
        $this->rmaBuilder = $rmaBuilder;
        $this->itemMapper = $itemMapper;
        $this->trackReadService = $trackReadService;
        $this->commentReadService = $commentReadService;
    }

    /**
     * Returns list of Item
     *
     * @param \Magento\Rma\Model\Resource\Item\Collection $itemCollection
     * @return array
     */
    public function getMappedItems(\Magento\Rma\Model\Resource\Item\Collection $itemCollection)
    {
        $dtoItems = [];
        foreach ($itemCollection as $itemModel) {
            $dtoItems[] = $this->itemMapper->extractDto($itemModel);
        }

        return $dtoItems;
    }

    /**
     * Returns list of comments
     *
     * @param $rmaId
     * @return array
     */
    public function getMappedComments($rmaId)
    {
        $commentsResult = $this->commentReadService->commentsList($rmaId);
        return $commentsResult->getItems();
    }

    /**
     * @param \Magento\Rma\Model\Rma $rmaModel
     * @return \Magento\Rma\Service\V1\Data\Rma
     */
    public function extractDto(\Magento\Rma\Model\Rma $rmaModel)
    {
        $this->rmaBuilder->populateWithArray($rmaModel->getData());
        $this->rmaBuilder->setItems($this->getMappedItems($rmaModel->getItemsForDisplay()));
        $this->rmaBuilder->setComments($this->getMappedComments($rmaModel->getId()));
        $this->rmaBuilder->setTracks($this->trackReadService->getTracks($rmaModel->getId()));
        return $this->rmaBuilder->create();
    }
}
