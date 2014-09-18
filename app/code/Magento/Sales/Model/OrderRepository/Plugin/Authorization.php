<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Sales\Model\OrderRepository\Plugin;

use \Magento\Authorization\Model\UserContextInterface;
use \Magento\Framework\Exception\NoSuchEntityException;

class Authorization
{
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext
    ) {
        $this->userContext = $userContext;
    }

    /**
     * Checks if order is allowed
     *
     * @param \Magento\Sales\Model\OrderRepository $subject
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGet(
        \Magento\Sales\Model\OrderRepository $subject,
        \Magento\Sales\Model\Order $order
    ) {
        if (!$this->isAllowed($order)) {
            throw NoSuchEntityException::singleField('orderId', $order->getId());
        }
        return $order;
    }

    /**
     * Checks if order is allowed for current customer
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function isAllowed(\Magento\Sales\Model\Order $order)
    {
        return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER
            ? $order->getCustomerId() == $this->userContext->getUserId()
            : true;
    }
}