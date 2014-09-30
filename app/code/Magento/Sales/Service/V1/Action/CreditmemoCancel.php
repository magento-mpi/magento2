<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\CreditmemoRepository;

/**
 * Class CreditmemoCancel
 */
class CreditmemoCancel
{
    /**
     * @var CreditmemoRepository
     */
    protected $creditmemoRepository;

    /**
     * @param CreditmemoRepository $creditmemoRepository
     */
    public function __construct(CreditmemoRepository $creditmemoRepository)
    {
        $this->creditmemoRepository = $creditmemoRepository;
    }

    /**
     * Invoke CreditmemoCancel service
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return (bool)$this->creditmemoRepository->get($id)->cancel();
    }
}
