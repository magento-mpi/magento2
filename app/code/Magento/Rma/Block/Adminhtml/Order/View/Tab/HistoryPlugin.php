<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Order\View\Tab;

use Magento\Framework\Stdlib\DateTime\Date;
use Magento\Rma\Model\Rma\Source\Status;
use Magento\Sales\Block\Adminhtml\Order\View\Tab\History;
use Magento\Rma\Model\Resource\Rma\Status\History\CollectionFactory as HistoryCollectionFactory;
use Magento\Rma\Model\Resource\Rma\Collection;
use Magento\Rma\Model\Rma\Status\History as StatusHistory;

/**
 * Class HistoryPlugin
 * @package Magento\Rma\Block\Adminhtml\Order\View\Tab
 */
class HistoryPlugin
{
    /**
     * @var Collection
     */
    private $rmaCollection;

    /**
     * @var HistoryCollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @param Collection $rmaCollection
     * @param HistoryCollectionFactory $historyCollectionFactory
     */
    public function __construct(Collection $rmaCollection, HistoryCollectionFactory $historyCollectionFactory)
    {
        $this->rmaCollection = $rmaCollection;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * Add Returns to Order Comments history
     *
     * @param History $subject
     * @param array $history
     * @return array
     */
    public function afterGetFullHistory(History $subject, array $history)
    {
        $collection = $this->rmaCollection->addFieldToFilter('order_id', $subject->getOrder()->getId())->load();
        $creationSystemComment = StatusHistory::getSystemCommentByStatus(Status::STATE_PENDING);
        /** @var \Magento\Rma\Model\Rma $rma */
        foreach ($collection as $rma) {
            /** @var $collection \Magento\Rma\Model\Resource\Rma\Status\History\Collection */
            $historyCollection = $this->historyCollectionFactory->create();
            /** @var $comments \Magento\Rma\Model\Rma\Status\History[] */
            $comments = $historyCollection->addFieldToFilter('rma_entity_id', $rma->getId())->load();
            foreach ($comments as $comment) {
                if ($comment['comment'] == $creationSystemComment) {
                    $history[] = [
                        'title' => sprintf('Return #%s created', $rma->getId()),
                        'notified' => $comment['is_customer_notified'],
                        'comment' => '',
                        'created_at' => $comment->getCreatedAtDate()
                    ];
                }
            }

        }
        usort($history, [get_class($subject), "sortHistoryByTimestamp"]);
        return $history;
    }
} 