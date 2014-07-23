<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

use Magento\Sales\Model\Resource\Order\Status\History\CollectionFactory;

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
     * @param CollectionFactory $historyCollectionFactory
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(
        CollectionFactory $historyCollectionFactory,
        \Magento\Framework\ObjectManager $objectManager
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->objectManager = $objectManager;
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
            $order->sendNewOrderEmail();
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