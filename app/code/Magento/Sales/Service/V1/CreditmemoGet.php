<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\Order\CreditmemoRepository;
use Magento\Sales\Service\V1\Data\CreditmemoMapper;

/**
 * Class CreditmemoGet
 */
class CreditmemoGet implements CreditmemoGetInterface
{
    /**
     * @var CreditmemoRepository
     */
    protected $creditmemoRepository;

    /**
     * @var CreditmemoMapper
     */
    protected $creditmemoMapper;

    /**
     * @param CreditmemoRepository $creditmemoRepository
     * @param CreditmemoMapper $creditmemoMapper
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
     * @return \Magento\Framework\Service\Data\AbstractObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return $this->creditmemoMapper->extractDto($this->creditmemoRepository->get($id));
    }
}
