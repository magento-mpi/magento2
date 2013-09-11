<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order shippment API V2
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Shipment\Api;

class V2 extends \Magento\Sales\Model\Order\Shipment\Api
{
    protected function _prepareItemQtyData($data)
    {
        $_data = array();
        foreach ($data as $item) {
            if (isset($item->order_item_id) && isset($item->qty)) {
                $_data[$item->order_item_id] = $item->qty;
            }
        }
        return $_data;
    }

    /**
     * Create new shipment for order.
     *
     * @param string $orderIncrementId
     * @param array $itemsQty Array of objects in format: object('order_item_id' => $itemId, 'qty' => $qty)
     * @param string $comment
     * @param bool $email Should email be sent to customer
     * @param bool $includeComment Should comment be included in the email or not.
     * @return string Shipment increment ID
     */
    public function create(
        $orderIncrementId,
        $itemsQty = array(),
        $comment = null,
        $email = false,
        $includeComment = false
    ) {
        /** @var \Magento\Sales\Model\Order $order */
        $order = \Mage::getModel('\Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);
        $itemsQty = $this->_prepareItemQtyData($itemsQty);
        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }
        /**
         * Check shipment create availability
         */
        if (!$order->canShip()) {
            $this->_fault('data_invalid', __('We cannot create a shipment for this order.'));
        }
        /* @var $shipment \Magento\Sales\Model\Order\Shipment */
        $shipment = $order->prepareShipment($itemsQty);
        if ($shipment) {
            $shipment->register();
            $shipment->addComment($comment, $email && $includeComment);
            if ($email) {
                $shipment->setEmailSent(true);
            }
            $shipment->getOrder()->setIsInProcess(true);
            try {
                $transactionSave = \Mage::getModel('\Magento\Core\Model\Resource\Transaction');
                $transactionSave->addObject($shipment)->addObject($shipment->getOrder())->save();
                $shipment->sendEmail($email, ($includeComment ? $comment : ''));
            } catch (\Magento\Core\Exception $e) {
                $this->_fault('data_invalid', $e->getMessage());
            }
            return $shipment->getIncrementId();
        }
        return null;
    }

    /**
     * Retrieve allowed shipping carriers for specified order
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function getCarriers($orderIncrementId)
    {
        $order = \Mage::getModel('\Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);

        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }
        $carriers = array();
        foreach ($this->_getCarriers($order) as $key => $value) {
            $carriers[] = array('key' => $key, 'value' => $value);
        }

        return $carriers;
    }
}
