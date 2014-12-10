<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Service\V1;

interface ReadServiceInterface
{
    /**
     * @param int $id
     * @return \Magento\SalesArchive\Service\V1\Data\Archive
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderInfo($id);
}
