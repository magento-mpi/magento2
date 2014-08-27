<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\CreditmemoRepository;
use Magento\Sales\Service\V1\Data\CreditmemoMapper;

/**
 * Class CreditmemoGet
 */
class CreditmemoGet
{
    /**
     * @var \Magento\Sales\Model\Order\CreditmemoRepository
     */
    protected $creditmemoRepository;

    /**
     * @var \Magento\Sales\Service\V1\Data\CreditmemoMapper
     */
    protected $creditmemoMapper;

    /**
     * @param \Magento\Sales\Model\Order\CreditmemoRepository $creditmemoRepository
     * @param \Magento\Sales\Service\V1\Data\CreditmemoMapper $creditmemoMapper
     */
    public function __construct(
        CreditmemoRepository $creditmemoRepository,
        CreditmemoMapper $creditmemoMapper
    ) {
        $this->creditmemoRepository = $creditmemoRepository;
        $this->creditmemoMapper = $creditmemoMapper;
    }

    /**
     * Invoke creditmemo get service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Creditmemo
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return $this->creditmemoMapper->extractDto($this->creditmemoRepository->get($id));
    }
}
