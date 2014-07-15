<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\AdminOrder;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Logger;
use Magento\Sales\Model\Order;

/**
 * Class EmailSender
 */
class EmailSender
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @param ManagerInterface $messageManager
     * @param Logger $logger
     */
    public function __construct(ManagerInterface $messageManager, Logger $logger)
    {
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    /**
     * Send email about new order.
     * Process mail exception
     *
     * @param Order $order
     * @return bool
     */
    public function send(Order $order)
    {
        try {
            $order->sendNewOrderEmail();
        } catch (\Magento\Framework\Mail\Exception $exception) {
            $this->logger->logException($exception);
            $this->messageManager->addWarning(
                __('You did not email your customer. Please check your email settings.')
            );
            return false;
        }

        return true;
    }
}
