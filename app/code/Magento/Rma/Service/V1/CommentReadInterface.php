<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Service\V1;

use Magento\Framework\Api\SearchCriteria;

interface CommentReadInterface
{
    /**
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResults
     */
    public function commentsList($id);
}
