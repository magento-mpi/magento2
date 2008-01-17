<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml invoice create form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Form extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/invoice/create/form.phtml');
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'items',
            $this->getLayout()->createBlock('adminhtml/sales_order_invoice_create_items')
        );
        $paymentInfoBlock = $this->getLayout()->createBlock('adminhtml/sales_order_payment')
            ->setPayment($this->getInvoice()->getOrder()->getPayment());
        $this->setChild('payment_info', $paymentInfoBlock);

        return parent::_prepareLayout();
    }

    /**
     * Check shipment availability for current invoice
     *
     * @return bool
     */
    public function canCreateShipment()
    {
        foreach ($this->getInvoice()->getAllItems() as $item) {
        	if ($item->getOrderItem()->getQtyToShip()) {
        	    return true;
        	}
        }
        return false;
    }


    public function getSaveUrl()
    {
        return Mage::getUrl('*/*/save', array('order_id' => $this->getInvoice()->getOrderId()));
    }
}