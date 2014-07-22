<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sales\Model;

/**
 * Class Notifier
 * @package Magento\Sales\Model
 */
class Notifier extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Status\History\CollectionFactory
     */
    protected $historyCollectionFactory;

    public function __construct(
        \Magento\Sales\Model\Resource\Order\Status\History\CollectionFactory $historyCollectionFactory
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * Notify user
     *
     * @param Order $order
     * @return bool
     */
    public function notify(\Magento\Sales\Model\Order $order)
    {
        if ($order->sendNewOrderEmail()) {
            $historyItem = $this->historyCollectionFactory->create()->getUnnotifiedForInstance(
                $order,
                \Magento\Sales\Model\Order::HISTORY_ENTITY_NAME
            );
            if ($historyItem) {
                $historyItem->setIsCustomerNotified(1);
                $historyItem->save();
            }
            return true;
        }
        return false;
    }
}