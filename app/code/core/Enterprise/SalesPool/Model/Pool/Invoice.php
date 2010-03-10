<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Invoice manipulation model
 *
 */
class Enterprise_SalesPool_Model_Pool_Invoice extends Varien_Object
{
    /**
     * Put invoice data to order model
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Enterprise_SalesPool_Model_Pool_Invoice
     */
    public function saveToOrder(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $data = $invoice->getData();

        $invoice->setForceObjectSave(true); // Do not save to db

        $data['_items'] = array();
        $data['_comments'] = array();

        foreach ($invoice->getItemsCollection() as $item) {
            $data['_items'][] = $item->getData();
            $item->setForceObjectSave(true); // Do not save to db
        }

        foreach ($invoice->getCommentsCollection() as $comment) {
            $data['_comments'][] = $comment->getData();
            $comment->setForceObjectSave(true); // Do not save to db
        }

        $serialized = serialize($data);
        $order->setSerializedInvoiceData($serialized);
        return $this;
    }

    /**
     * Restore invoice from data stored in order
     *
     * @param $orderId
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param array $invoiceData
     * @return Enterprise_SalesPool_Model_Pool_Invoice
     */
    public function restoreFromOrder($orderId, Mage_Sales_Model_Order_Invoice $invoice, $invoiceData)
    {
        $invoice->setOrderId($orderId);
        if (!$invoiceData) {
            return $this;
        }

        $data = unserialize($invoiceData);

        $invoice->addData($data);

        foreach ($data['_items'] as $itemData) {
            $item = Mage::getModel('sales/order_invoice_item');
            $item->setData($itemData);
            $invoice->addItem($item);
        }

        foreach ($data['_comments'] as $commentData) {
            $comment = Mage::getModel('sales/order_invoice_comment');
            $comment->setData($commentData);
            $invoice->addComment($comment);
        }
        return $this;
    }
}
