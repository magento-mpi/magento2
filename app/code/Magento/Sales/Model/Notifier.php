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
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */

    protected $objectManager;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @param CollectionFactory $historyCollectionFactory
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param OrderSender $orderSender
     */
    public function __construct(
        CollectionFactory $historyCollectionFactory,
        \Magento\Framework\ObjectManager $objectManager,
        OrderSender $orderSender
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->objectManager = $objectManager;
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
        } catch (\Magento\Framework\Mail\Exception $e) {
            $this->objectManager->get('Magento\Framework\Logger')->logException($e);
            return false;
        }
        return true;
    }
}
