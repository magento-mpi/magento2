<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\Comment;
use Magento\Sales\Service\V1\Data\Creditmemo;

interface CreditmemoWriteInterface
{
    /**
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     * @throws \Exception
     */
    public function addComment(Comment $comment);

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function cancel($id);

    /**
     * @param int $id
     * @return bool
     */
    public function email($id);

    /**
     * @param \Magento\Sales\Service\V1\Data\Creditmemo $creditmemoDataObject
     * @throws \Exception
     * @return bool
     */
    public function create(Creditmemo $creditmemoDataObject);
}
