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
 * Invoice API
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Invoice_Api extends Magento_Sales_Model_Api_Resource
{
    /**
     * @param Magento_Api_Helper_Data $apiHelper
     */
    public function __construct(
        Magento_Api_Helper_Data $apiHelper
    ) {
        parent::__construct($apiHelper);
        $this->_attributesMap = array(
            'invoice' => array('invoice_id' => 'entity_id'),
            'invoice_item' => array('item_id' => 'entity_id'),
            'invoice_comment' => array('comment_id' => 'entity_id')
        );
    }

    /**
     * Retrive invoices list. Filtration could be applied
     *
     * @param null|object|array $filters
     * @return array
     */
    public function items($filters = null)
    {
        $invoices = array();
        /** @var $invoiceCollection Magento_Sales_Model_Resource_Order_Invoice_Collection */
        $invoiceCollection = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Invoice_Collection');
        $invoiceCollection->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('order_id')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('state')
            ->addAttributeToSelect('grand_total')
            ->addAttributeToSelect('order_currency_code');

        try {
            $filters = $this->_apiHelper->parseFilters($filters, $this->_attributesMap['invoice']);
            foreach ($filters as $field => $value) {
                $invoiceCollection->addFieldToFilter($field, $value);
            }
        } catch (Magento_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        foreach ($invoiceCollection as $invoice) {
            $invoices[] = $this->_getAttributes($invoice, 'invoice');
        }
        return $invoices;
    }

    /**
     * Retrieve invoice information
     *
     * @param string $invoiceIncrementId
     * @return array
     */
    public function info($invoiceIncrementId)
    {
        $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var Magento_Sales_Model_Order_Invoice $invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }

        $result = $this->_getAttributes($invoice, 'invoice');
        $result['order_increment_id'] = $invoice->getOrderIncrementId();

        $result['items'] = array();
        foreach ($invoice->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'invoice_item');
        }

        $result['comments'] = array();
        foreach ($invoice->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'invoice_comment');
        }

        return $result;
    }

    /**
     * Create new invoice for order
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param boolean $email
     * @param boolean $includeComment
     * @return string
     */
    public function create($orderIncrementId, $itemsQty, $comment = null, $email = false, $includeComment = false)
    {
        $order = Mage::getModel('Magento_Sales_Model_Order')->loadByIncrementId($orderIncrementId);

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
            $transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $invoice->sendEmail($email, ($includeComment ? $comment : ''));
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $invoice->getIncrementId();
    }

    /**
     * Add comment to invoice
     *
     * @param string $invoiceIncrementId
     * @param string $comment
     * @param boolean $email
     * @param boolean $includeComment
     * @return boolean
     */
    public function addComment($invoiceIncrementId, $comment, $email = false, $includeComment = false)
    {
        $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var $invoice Magento_Sales_Model_Order_Invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }


        try {
            $invoice->addComment($comment, $email);
            $invoice->sendUpdateEmail($email, ($includeComment ? $comment : ''));
            $invoice->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Capture invoice
     *
     * @param string $invoiceIncrementId
     * @return boolean
     */
    public function capture($invoiceIncrementId)
    {
        $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var $invoice Magento_Sales_Model_Order_Invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }

        if (!$invoice->canCapture()) {
            $this->_fault(
                'status_not_changed',
                __('Sorry, but we cannot capture the invoice.')
            );
        }

        try {
            $invoice->capture();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault(
                'status_not_changed',
                __('Sorry, but something went wrong capturing the invoice.')
            );
        }

        return true;
    }

    /**
     * Void invoice
     *
     * @param unknown_type $invoiceIncrementId
     * @return unknown
     */
    public function void($invoiceIncrementId)
    {
        $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var $invoice Magento_Sales_Model_Order_Invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }

        if (!$invoice->canVoid()) {
            $this->_fault(
                'status_not_changed',
                __('Sorry, but we cannot void the invoice.')
            );
        }

        try {
            $invoice->void();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('status_not_changed', __('Invoice void problem'));
        }

        return true;
    }

    /**
     * Cancel invoice
     *
     * @param string $invoiceIncrementId
     * @return boolean
     */
    public function cancel($invoiceIncrementId)
    {
        $invoice = Mage::getModel('Magento_Sales_Model_Order_Invoice')->loadByIncrementId($invoiceIncrementId);

        /* @var $invoice Magento_Sales_Model_Order_Invoice */

        if (!$invoice->getId()) {
            $this->_fault('not_exists');
        }

        if (!$invoice->canCancel()) {
            $this->_fault(
                'status_not_changed',
                __('Sorry, but we cannot cancel the invoice.')
            );
        }

        try {
            $invoice->cancel();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = Mage::getModel('Magento_Core_Model_Resource_Transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault(
                'status_not_changed',
                __('Something went wrong canceling the invoice.')
            );
        }

        return true;
    }
}
