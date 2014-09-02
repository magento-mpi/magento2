<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Action\InvoiceAddComment;
use Magento\Sales\Service\V1\Action\InvoiceVoid;
use Magento\Sales\Service\V1\Action\InvoiceEmail;
use Magento\Sales\Service\V1\Action\InvoiceCapture;
use Magento\Sales\Service\V1\Action\InvoiceCreate;
use Magento\Sales\Service\V1\Data\Comment;

/**
 * Class InvoiceWrite
 */
class InvoiceWrite implements InvoiceWriteInterface
{
    /**
     * @var InvoiceAddComment
     */
    protected $invoiceAddComment;

    /**
     * @var InvoiceVoid
     */
    protected $invoiceVoid;

    /**
     * @var InvoiceEmail
     */
    protected $invoiceEmail;

    /**
     * @var InvoiceCapture
     */
    protected $invoiceCapture;

    /**
     * @var InvoiceCreate
     */
    protected $invoiceCreate;

    /**
     * @param InvoiceAddComment $invoiceAddComment
     * @param InvoiceVoid $invoiceVoid
     * @param InvoiceEmail $invoiceEmail
     * @param InvoiceCapture $invoiceCapture
     * @param InvoiceCreate $invoiceCreate
     */
    public function __construct(
        InvoiceAddComment $invoiceAddComment,
        InvoiceVoid $invoiceVoid,
        InvoiceEmail $invoiceEmail,
        InvoiceCapture $invoiceCapture,
        InvoiceCreate $invoiceCreate
    ) {
        $this->invoiceAddComment = $invoiceAddComment;
        $this->invoiceVoid = $invoiceVoid;
        $this->invoiceEmail = $invoiceEmail;
        $this->invoiceCapture = $invoiceCapture;
        $this->invoiceCreate = $invoiceCreate;
    }

    /**
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     * @throws \Exception
     */
    public function addComment(Comment $comment)
    {
        return $this->invoiceAddComment->invoke($comment);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function void($id)
    {
        return $this->invoiceVoid->invoke($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function email($id)
    {
        return $this->invoiceEmail->invoke($id);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function capture($id)
    {
        return $this->invoiceCapture->invoke($id);
    }

    /**
     * @param \Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject
     * @return bool
     * @throws \Exception
     */
    public function create(\Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject)
    {
        return $this->invoiceCreate->invoke($invoiceDataObject);
    }
}
