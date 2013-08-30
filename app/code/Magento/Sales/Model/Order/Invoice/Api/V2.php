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
 * Invoice API V2
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Invoice_Api_V2 extends Magento_Sales_Model_Order_Invoice_Api
{
    /**
     * Create new invoice for order
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param bool $email
     * @param bool $includeComment
     * @return string
     */
    public function create($orderIncrementId, $itemsQty, $comment = null, $email = false, $includeComment = false)
    {
        $order = Mage::getModel('Magento_Sales_Model_Order')->loadByIncrementId($orderIncrementId);
        $itemsQty = $this->_prepareItemQtyData($itemsQty);
        /* @var $order Magento_Sales_Model_Order */
        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }

        /**
         * Check invoice create availability
         */
        if (!$order->canInvoice()) {
            $this->_fault('data_invalid', __('We cannot create an invoice for this order.'));
        }

        $invoice = $order->prepareInvoice($itemsQty);

        $invoice->register();

        if ($comment !== null) {
            $invoice->addComment($comment, $email);
        }

        if ($email) {
            $invoice->setEmailSent(true);
        }

        $invoice->getOrder()->setIsInProcess(true);

        try {
            Mage::getModel('Magento_Core_Model_Resource_Transaction')->addObject($invoice)->addObject($invoice->getOrder())
                ->save();
            $invoice->sendEmail($email, ($includeComment ? $comment : ''));
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $invoice->getIncrementId();
    }

    /**
     * Prepare items quantity data
     *
     * @param array $data
     * @return array
     */
    protected function _prepareItemQtyData($data)
    {
        $quantity = array();
        foreach ($data as $item) {
            if (isset($item->order_item_id) && isset($item->qty)) {
                $quantity[$item->order_item_id] = $item->qty;
            }
        }
        return $quantity;
    }
}
