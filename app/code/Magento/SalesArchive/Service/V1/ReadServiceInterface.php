<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
