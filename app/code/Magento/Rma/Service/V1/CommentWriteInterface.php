<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Service\V1;

use Magento\Rma\Service\V1\Data\RmaStatusHistory;

interface CommentWriteInterface
{
    /**
     * @param int $id
     * @param \Magento\Rma\Service\V1\Data\RmaStatusHistory $data
     * @return bool
     * @throws \Exception
     */
    public function addComment($id, RmaStatusHistory $data);
}
