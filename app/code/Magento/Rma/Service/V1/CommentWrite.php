<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\Rma\Service\V1\Data\RmaStatusHistory;
use Magento\Rma\Model\RmaRepository;

class CommentWrite implements CommentWriteInterface
{
    /**
     * @var \Magento\Rma\Model\Rma\Status\History
     */
    protected $statusHistory;

    /**
     * @var RmaRepository
     */
    protected $rmaRepository;

    /**
     * @param RmaRepository $rmaRepository
     * @param \Magento\Rma\Model\Rma\Status\History $statusHistory
     */
    public function __construct(
        RmaRepository $rmaRepository,
        \Magento\Rma\Model\Rma\Status\History $statusHistory
    ) {
        $this->rmaRepository = $rmaRepository;
        $this->statusHistory = $statusHistory;
    }

    /**
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\RmaStatusHistory $data
     * @return bool
     * @throws \Exception
     */
    public function addComment($id, RmaStatusHistory $data)
    {
        $comment = trim($data->getComment());
        if (!$comment) {
            throw new \Magento\Framework\Exception\InputException(__('Please enter a valid comment.'));
        }
        $this->statusHistory->setComment($comment)
            ->setRma($this->rmaRepository->get($id));

        if ($data->isCustomerNotified()) {
            $this->statusHistory->sendCustomerCommentEmail();
        }
        $this->statusHistory->saveComment($comment, $data->isVisibleOnFront(), true);
        return true;
    }
}
