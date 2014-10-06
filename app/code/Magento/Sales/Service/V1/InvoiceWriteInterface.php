<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\Comment;

interface InvoiceWriteInterface
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
    public function void($id);

    /**
     * @param int $id
     * @return bool
     */
    public function email($id);

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function capture($id);

    /**
     * @param \Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject
     * @return bool
     * @throws \Exception
     */
    public function create(\Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject);
}
