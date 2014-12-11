<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Service\V1;


interface CommentReadInterface
{
    /**
     * @param int $id
     * @return \Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResults
     */
    public function commentsList($id);
}
