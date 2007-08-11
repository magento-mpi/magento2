<?php
/**
 * Adminhtml sales orders controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Orders'), __('Orders'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order'))
            ->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('order_id');
        $model = Mage::getModel('sales/order');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('sales_order', $model);

        $this->_initAction()
            ->_addBreadcrumb(__('Edit Order'), __('Edit Order'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order_edit'))
            ->renderLayout();
    }

    public function viewAction()
    {
        $id = $this->getRequest()->getParam('order_id');
        $model = Mage::getModel('sales/order');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(__('This order no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        Mage::register('sales_order', $model);

        $this->_initAction()
            ->_addBreadcrumb(__('View Order'), __('View Order'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/sales_order_view'))
            ->renderLayout();
    }

    public function testAction()
    {
        $customers = Mage::getResourceModel('customer/customer_collection')->load()->getItems();
        $customerIds = array_keys($customers);
        $customerId = $customerIds[ rand(0, count($customerIds)-1) ];

        $statusIds =  Mage::getResourceModel('sales/order_status_collection')->load()->getItems();


        $order_data = array(
            'real_order_id' => md5(time()),
            'customer_id' => $customerId,
            'remote_ip' => '192.168.0.1',
            'order_status_id' => $statusIds[rand(0, count($statusIds)-1)],
            'quote_id' => '1',
            'quote_address_id' => '1',
            'billing_address_id' => '0',
            'shipping_address_id' => '0',
            'coupon_code' => 'ZGDS67RX35',
            'giftcert_code' => 'ZGDS67RX35',
            'base_currency_code' => 'USD',
            'store_currency_code' => 'USD',
            'order_currency_code' => 'USD',
            'store_to_base_rate' => '1.00',
            'store_to_order_rate' => '1.00',
            'is_virtual' => '0',
            'is_multi_payment' => '0',
            'weight' => '1.2',
            'shipping_method' => 'UPS',
            'shipping_description' => 'Express Night',
            'subtotal' => '175.43',
            'tax_amount' => '5.43',
            'shipping_amount' => '15.00',
            'discount_amount' => '7.00',
            'giftcert_amount' => '3.00',
            'custbalance_amount' => '2.00',
            'grand_total' => '183.86',
            'total_paid' => '14.00',
            'total_due' => '169.86',
            'customer_notes' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'total_qty_ordered' => rand(2,23),
        );
        $order = Mage::getModel('sales/order')->setData($order_data);

        $order->setCustomer($customers[$customerId]);

        $address_data = array(
            'parent_id' => '',
            'quote_address_id' => '1',
//            'address_type' => 'billing',
            'customer_id' => $customerId,
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

        $order->setShippingAddress(Mage::getModel('sales/order_address')->setData($address_data));

//        $address_data['address_type'] = 'shipping';
        $order->setBillingAddress(Mage::getModel('sales/order_address')->setData($address_data));

        $payment_data = array(
            'parent_id' => '',
            'quote_payment_id' => '1',
            'customer_payment_id' => '1',
            'amount' => '100.23',
            'method' => 'Credit Card',
            'po_number' => '111',
            'cc_type' => 'Visa',
            'cc_number_enc' => 'AB&*(AHKO"%*D*6',
            'cc_last4' => '4571',
            'cc_owner' => 'QA Tester',
            'cc_exp_month' => '11',
            'cc_exp_year' => '2010',
            'cc_trans_id' => '1A3BD94AF78FE89',
            'cc_approval' => '',
            'cc_avs_status' => 'Valid',
            'cc_cid_status' => 'Valid',
            'cc_debug_request' => 'asdf  asdpuhwaefu asdifuhawe9f sdfuhwefuhfs   iuwef',
            'cc_debug_response' => 'aklsjhdasf w9ehr;k  asdpufwrefp  asdufhwref ',
        );

        $order->addPayment(Mage::getModel('sales/order_payment')->setData($payment_data));

        $products = Mage::getResourceModel('catalog/product_collection')->load()->getItems();
        $productIds = array_keys($products);

        $item_data = array(
            'parent_id' => '',
            'quote_item_id' => '1',
            'qty_ordered' => rand(1,10),
            'qty_backordered' => '',
            'qty_canceled' => '',
            'qty_shipped' => '',
            'qty_returned' => '',
            'cost' => '56',
            'discount_percent' => '12',
            'discount_amount' => '44',
            'tax_percent' => '8.00',
        );

        for ($i = 0, $n = rand(3,8); $i < $n; $i++) {
            $product = $products[ $productIds[ rand(0, count($productIds)-1) ] ];
            $product->load($product->getId());
            $item_data['product_id'] = $product->getId();
            $item_data['sku'] = $product->getSku();
            $item_data['image'] = '';
            $item_data['name'] = $product->getName();
            $item_data['price'] = $product->getProductPrice();
            $item = Mage::getModel('sales/order_item')->setData($item_data)->calcRowTotal()->calcRowWeight()->calcTaxAmount();
            $order->addItem($item);
        }

        $order->save();
        $order_id = $order->getEntityId();

        echo 'Ok - ' . $order_id . "\n<br/>\n";

    }

}
