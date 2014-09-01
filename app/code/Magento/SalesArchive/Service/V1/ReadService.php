<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Service\V1;

use Magento\Sales\Model\OrderRepository;
use Magento\SalesArchive\Service\V1\Data\ArchiveMapper;

class ReadService implements ReadServiceInterface
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ArchiveMapper
     */
    protected $archiveMapper;

    /**
     * @param OrderRepository $orderRepository
     * @param ArchiveMapper $archiveMapper
     */
    public function __construct(
        OrderRepository $orderRepository,
        ArchiveMapper $archiveMapper
    ) {
        $this->orderRepository = $orderRepository;
        $this->archiveMapper = $archiveMapper;
    }

    /**
     * Retrieve order info service
     *
     * @param int $id
     * @return \Magento\SalesArchive\Service\V1\Data\Archive
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderInfo($id)
    {
        $order = $this->orderRepository->get($id);
        return $this->archiveMapper->extractDto($order);
    }
}
