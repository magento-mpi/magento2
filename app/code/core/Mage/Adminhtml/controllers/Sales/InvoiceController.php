<?php
/**
 * Adminhtml sales invoices controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Sales_InvoiceController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('sales/invoice')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Invoices'), __('Invoices'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_invoice'))
            ->renderLayout();
    }

    public function newAction()
    {
        if ($orderId = $this->getRequest()->getParam('order_id')) {
            $order = Mage::getModel('sales/order');

            if ($orderId) {
                $order->load($orderId);
                if (! $order->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
                $this->_redirect('*/*/');
                return;
            }

            $model = Mage::getModel('sales/invoice');
            $model->createFromOrder($order);

            // set entered data if was error when we do save
            $data = Mage::getSingleton('adminhtml/session')->getInvoiceData(true);
            if (! empty($data)) {
                $model->setData($data);
            }

            Mage::register('sales_invoice', $model);

            $this->_initAction()
                ->_addBreadcrumb(('Create New Invoice'), __('Create New Invoice'))
                ->_addContent($this->getLayout()->createBlock('adminhtml/sales_invoice_create'))
                ->renderLayout();
        } else {
            $this->_redirect('*/sales_order/');
        }
    }

    public function cmemoAction()
    {
        if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            $invoice = Mage::getModel('sales/invoice');

            if ($invoiceId) {
                $invoice->load($invoiceId);
                if (! $invoice->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(__('This invoice no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(__('This invoice no longer exists'));
                $this->_redirect('*/*/');
                return;
            }

            $model = Mage::getModel('sales/invoice');
            $model->createFromInvoice($invoice);

            // set entered data if was error when we do save
            $data = Mage::getSingleton('adminhtml/session')->getInvoiceData(true);
            if (! empty($data)) {
                $model->setData($data);
            }

            Mage::register('sales_invoice', $model);

            $this->_initAction()
                ->_addBreadcrumb(('Create New Credit Memo'), __('Create New Credit Memo'))
                ->_addContent($this->getLayout()->createBlock('adminhtml/sales_cmemo_create'))
                ->renderLayout();
        } else {
            $this->_redirect('*/sales_invoice/');
        }
    }


    public function savenewAction()
    {
        if (($orderId = $this->getRequest()->getParam('order_id')) && ($data = $this->getRequest()->getPost())) {

            Mage::getSingleton('adminhtml/session')->addNotice(print_r($data, true));

            $invoice = Mage::getModel('sales/invoice');
            $invoice->setData($data);

            $order = Mage::getModel('sales/order')->load($orderId);
            if (! $order->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('The order you are trying to create invoice for, no longer exists'));
                Mage::getSingleton('adminhtml/session')->setInvoiceData($data);
                $this->_redirect('*/sales_invoice/new/', array('order_id' => $orderId));
                return;
            }
            $invoice->createFromOrder($order);

            Mage::getSingleton('adminhtml/session')->addNotice($invoice->getInvoiceType());

            try {
                $invoice->setData('items', $data['items']);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(__('Invoice was not saved'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setInvoiceData($data);
                $this->_redirect('*/sales_invoice/new/', array('order_id' => $orderId));
                return;
            }

            try {
                $invoice->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Invoice was saved succesfully'));
                Mage::getSingleton('adminhtml/session')->setInvoiceData(false);
                if (Mage_Sales_Model_Invoice::STATUS_OPEN == $invoice->getInvoiceStatusId()) {
                    try {
                        $invoice->processPayment();
                        // TODO - redirect to print form
                        Mage::getSingleton('adminhtml/session')->addSuccess(__('Invoice was charged succesfully'));
                        $this->_redirect('*/sales_invoice/edit/', array('invoice_id' => $invoice->getId()));
                        return;
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError(__('Invoice was not charged'));
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/sales_invoice/edit/', array('invoice_id' => $invoice->getId()));
                        return;
                    }
                }
                // TODO - redirect to print form
                $this->_redirect('*/sales_invoice/edit/', array('invoice_id' => $invoice->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(__('Invoice was not saved'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setInvoiceData($data);
                $this->_redirect('*/sales_invoice/new/', array('order_id' => $orderId));
                return;
            }
        } else {
            $this->_redirect('*/sales_order/');
        }
    }

    public function saveAction()
    {
        if (($invoiceId = $this->getRequest()->getParam('invoice_id')) && ($data = $this->getRequest()->getPost())) {
            $invoice = Mage::getModel('sales/invoice');
            $invoice->setData($data);
            try {
                $invoice->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Invoice was updated succesfully'));
                Mage::getSingleton('adminhtml/session')->setInvoiceData(false);
                if (Mage_Sales_Model_Invoice::STATUS_OPEN == $invoice->getInvoiceStatusId()) {
                    try {
                        // TODO
//                        $invoice->charge();
                        // TODO - redirect to print form
                        Mage::getSingleton('adminhtml/session')->addSuccess(__('Invoice was charged succesfully'));
                        $this->_redirect('*/sales_invoice/edit/', array('invoice_id' => $invoiceId));
                        return;
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError(__('Invoice was not charged'));
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/sales_invoice/edit/', array('invoice_id' => $invoiceId));
                        return;
                    }
                }
                // TODO - redirect to print form
                $this->_redirect('*/sales_invoice/edit/', array('invoice_id' => $invoiceId));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(__('Invoice was not saved'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setInvoiceData($data);
                $this->_redirect('*/sales_invoice/edit/', array('invoice_id' => $invoiceId));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function editAction()
    {
        if ($id = $this->getRequest()->getParam('invoice_id')) {
            $model = Mage::getModel('sales/invoice');

            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This invoice no longer exists'));
                $this->_redirect('*/*/');
                return;
            }

            // set entered data if was error when we do save
            $data = Mage::getSingleton('adminhtml/session')->getInvoiceData(true);
            if (! empty($data)) {
                $model->setData($data);
            }

            Mage::register('sales_invoice', $model);

            $this->_initAction()
                ->_addBreadcrumb(('Edit Invoice'), __('Edit Invoice'))
                ->_addContent($this->getLayout()->createBlock('adminhtml/sales_invoice_edit'))
                ->renderLayout();
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('invoice_id');
        $model = Mage::getModel('sales/invoice');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This invoice no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('This invoice no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('sales_invoice', $model);

        if (Mage_Sales_Model_Invoice::TYPE_CMEMO == $model->getInvoiceType()) {
            $type = 'cmemo';
        } else {
            $type = 'invoice';
        }

        $this->_initAction()
            ->_addBreadcrumb(('View Invoice'), __('View Invoice'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_' . $type . '_view'))
            ->renderLayout();
    }

    public function testAction()
    {
        $orders = Mage::getResourceModel('sales/order_collection')->load()->getItems();
        $orderIds = array_keys($orders);
        $order = Mage::getModel('sales/order')->load($orders[ $orderIds[ rand(0, count($orderIds)-1) ] ]);

        $statuses = Mage_Sales_Model_Invoice::getStatuses();
        $invoice_data = array(
            'customer_id' => $order->getCustomerId(),
            'order_id' => $order->getRealOrderId(),
            'remote_ip' => '192.168.0.1',
            'invoice_status_id' => $statusesp[rand(0, count($statuses)-1)],
            'billing_address_id' => '',
            'shipping_address_id' => '',
            'base_currency_code' => 'USD',
            'store_currency_code' => 'USD',
            'order_currency_code' => 'USD',
            'store_to_base_rate' => '1.00',
            'store_to_order_rate' => '1.00',
            'is_virtual' => '0',
            'subtotal' => '175.43',
            'tax_amount' => '5.43',
            'shipping_amount' => '15.00',
            'grand_total' => '183.86',
            'total_paid' => '14.00',
            'total_due' => '169.86',
        );
        $invoice = Mage::getModel('sales/invoice')->setData($invoice_data);

        $address_data = array(
            'parent_id' => $invoice_id,
            'order_address_id' => $order->getBillingAddress()->getEntityId(),
            'address_type' => 'billing',
            'customer_id' => '3',
            'customer_address_id' => '1',
            'email' => 'qa@varien.com',
            'firstname' => 'QA',
            'lastname' => 'Tester',
            'company' => 'Varien',
            'street' => 'Motor Ave',
            'city' => 'Los Angeles',
            'region' => 'California',
            'region_id' => '14',
            'postcode' => '12060',
            'country_id' => '223',
            'telephone' => '(123) 123-4567',
            'fax' => '(123) 123-4567',
        );

        $invoice->addAddress(Mage::getModel('sales/invoice_address')->setData($address_data));

        $address_data['address_type'] = 'shipping';
        $address_data['order_address_id'] = $order->getShippingAddress()->getEntityId();

        $invoice->addAddress(Mage::getModel('sales/invoice_address')->setData($address_data));

//        $payment_data = array(
//            'parent_id' => $invoice_id,
//            'order_payment_id' => $order->getPayment,
//            'amount' => '100.23',
//            'method' => 'Credit Card',
//            'cc_trans_id' => '1A3BD94AF78FE89',
//            'cc_approval' => '',
//            'cc_debug_request' => 'asdf  asdpuhwaefu asdifuhawe9f sdfuhwefuhfs   iuwef',
//            'cc_debug_response' => 'aklsjhdasf w9ehr;k  asdpufwrefp  asdufhwref ',
//        );
//
//        $payment = Mage::getModel('sales/invoice_payment')->setData($payment_data)->save();

        foreach ($order->getItemsCollection() as $orderItem) {
            $item_data = array(
                'parent_id' => '',
                'order_item_id' => $orderItem->getEntityId(),
                'product_id' => $orderItem->getProductId(),
                'product_name' => $orderItem->getName(),
                'sku' => $orderItem->getSku(),
                'qty' => $orderItem->getQtyOrdered(),
                'price' => $orderItem->getPrice(),
                'cost' => $orderItem->getCost(),
                'row_total' => $orderItem->getRowTotal(),
                'shipment_id' => '1',
            );
        }


        $shipment_data = array(
            'parent_id' => $invoice_id,
            'shipping_method' => 'USPS',
            'tracking_id' => md5(time()),
            'shipment_status_id' => Mage_Sales_Model_Invoice_Shipment::STATUS_SENT,
        );

        $invoice->addShipment(Mage::getModel('sales/invoice_shipment')->setData($shipment_data));

        $invoice->save();
        $invoice_id = $invoice->getEntityId();

        echo 'Ok - ' . $invoice_id . "\n<br/>\n";

    }

}
