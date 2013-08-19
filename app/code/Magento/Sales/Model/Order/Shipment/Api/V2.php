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
class Magento_Sales_Model_Order_Shipment_Api_V2 extends Magento_Sales_Model_Order_Shipment_Api
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
        /** @var Magento_Sales_Model_Order $order */
        $order = Mage::getModel('Magento_Sales_Model_Order')->loadByIncrementId($orderIncrementId);
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
        /* @var $shipment Magento_Sales_Model_Order_Shipment */
        $shipment = $order->prepareShipment($itemsQty);
        if ($shipment) {
            $shipment->register();
            $shipment->addComment($comment, $email && $includeComment);
            if ($email) {
                $shipment->setEmailSent(true);
            }
            $shipment->getOrder()->setIsInProcess(true);
            try {
                $transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction');
                $transactionSave->addObject($shipment)->addObject($shipment->getOrder())->save();
                $shipment->sendEmail($email, ($includeComment ? $comment : ''));
            } catch (Magento_Core_Exception $e) {
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
        $order = Mage::getModel('Magento_Sales_Model_Order')->loadByIncrementId($orderIncrementId);

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
