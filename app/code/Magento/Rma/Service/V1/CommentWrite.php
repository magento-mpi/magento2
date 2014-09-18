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
use Magento\Rma\Model\Rma\PermissionChecker;

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
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @param RmaRepository $rmaRepository
     * @param \Magento\Rma\Model\Rma\Status\History $statusHistory
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        RmaRepository $rmaRepository,
        \Magento\Rma\Model\Rma\Status\History $statusHistory,
        PermissionChecker $permissionChecker
    ) {
        $this->rmaRepository = $rmaRepository;
        $this->statusHistory = $statusHistory;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\RmaStatusHistory $data
     * @return bool
     * @throws \Exception
     */
    public function addComment($id, RmaStatusHistory $data)
    {
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkRmaForCustomerContext();

        $rmaModel = $this->rmaRepository->get($id);

        $comment = trim($data->getComment());
        if (!$comment) {
            throw new \Magento\Framework\Exception\InputException(__('Please enter a valid comment.'));
        }
        $this->statusHistory->setComment($comment)
            ->setRma($rmaModel);

        if ($data->isCustomerNotified()) {
            $this->statusHistory->sendCustomerCommentEmail();
        }
        $this->statusHistory->saveComment($comment, $data->isVisibleOnFront(), true);
        return true;
    }
}
