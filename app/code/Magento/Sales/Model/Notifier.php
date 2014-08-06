<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

use Magento\Sales\Model\Resource\Order\Status\History\CollectionFactory;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\Logger;
use Magento\Framework\Mail\Exception;

/**
 * Class Notifier
 * @package Magento\Sales\Model
 */
class Notifier extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @param CollectionFactory $historyCollectionFactory
     * @param Logger $logger
     * @param OrderSender $orderSender
     */
    public function __construct(
        CollectionFactory $historyCollectionFactory,
        Logger $logger,
        OrderSender $orderSender
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->logger = $logger;
        $this->orderSender = $orderSender;
    }

    /**
     * Notify user
     *
     * @param Order $order
     * @return bool
     * @throws \Magento\Framework\Mail\Exception
     */
    public function notify(\Magento\Sales\Model\Order $order)
    {
        try {
            $this->orderSender->send($order);
            if (!$order->getEmailSent()) {
                return false;
            }
            $historyItem = $this->historyCollectionFactory->create()->getUnnotifiedForInstance(
                $order,
                \Magento\Sales\Model\Order::HISTORY_ENTITY_NAME
            );
            if ($historyItem) {
                $historyItem->setIsCustomerNotified(1);
                $historyItem->save();
            }
        } catch (Exception $e) {
            $this->logger->logException($e);
            return false;
        }
        return true;
    }
}
