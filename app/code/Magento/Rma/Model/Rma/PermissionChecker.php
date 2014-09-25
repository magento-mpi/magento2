<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Model\Rma;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Rma\Model\Rma;
use Magento\Sales\Model\OrderRepository;
use Magento\Rma\Helper\Data;
use Magento\Framework\Exception\StateException;

class PermissionChecker
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var Data
     */
    private $rmaHelper;

    /**
     * @param UserContextInterface $userContext
     * @param OrderRepository $orderRepository
     * @param Data $rmaHelper
     */
    public function __construct(
        UserContextInterface $userContext,
        OrderRepository $orderRepository,
        Data $rmaHelper
    ) {
        $this->userContext = $userContext;
        $this->orderRepository = $orderRepository;
        $this->rmaHelper = $rmaHelper;
    }

    /**
     * Whether the user is the owner of the RMA
     *
     * @param Rma $rma
     * @return bool
     */
    public function isRmaOwner(Rma $rma)
    {
        return $this->isCustomerContext()
            ? $rma->getCustomerId() == $this->userContext->getUserId()
            : true;
    }

    /**
     * Verifies availability of rma for customer context
     *
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function checkRmaForCustomerContext()
    {
        if ($this->isCustomerContext() && !$this->rmaHelper->isEnabled()) {
            throw new StateException('Unknown service');
        }
        return true;
    }

    /**
     * Whether is the customer context
     *
     * @return bool
     */
    public function isCustomerContext()
    {
        return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER;
    }
}
