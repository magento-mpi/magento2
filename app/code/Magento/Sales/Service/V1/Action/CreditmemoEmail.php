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
 * Class CreditmemoEmail
 */
class CreditmemoEmail
{
    /**
     * @var CreditmemoRepository
     */
    protected $creditmemoRepository;

    /**
     * @var \Magento\Sales\Model\Order\CreditmemoNotifier
     */
    protected $creditmemoNotifier;

    /**
     * @param CreditmemoRepository $creditmemoRepository
     * @param \Magento\Sales\Model\Order\CreditmemoNotifier $notifier
     */
    public function __construct(
        CreditmemoRepository $creditmemoRepository,
        \Magento\Sales\Model\Order\CreditmemoNotifier $notifier
    ) {
        $this->creditmemoRepository = $creditmemoRepository;
        $this->creditmemoNotifier = $notifier;
    }

    /**
     * Invoke notifyUser service
     *
     * @param int $id
     * @return bool
     */
    public function invoke($id)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $this->creditmemoRepository->get($id);
        return $this->creditmemoNotifier->notify($creditmemo);
    }
}
