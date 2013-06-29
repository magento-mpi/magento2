<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml invoice items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    protected $_disableSubmitButton = false;

    /**
     * Prepare child blocks
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items
     */
    protected function _beforeToHtml()
    {
        $onclick = "submitAndReloadArea($('invoice_item_container'),'".$this->getUpdateUrl()."')";
        $this->addChild('update_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'class'     => 'update-button',
            'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Update Qty\'s'),
            'onclick'   => $onclick,
        ));
        $this->_disableSubmitButton = true;
        $_submitButtonClass = ' disabled';
        foreach ($this->getInvoice()->getAllItems() as $item) {
            /**
             * @see bug #14839
             */
            if ($item->getQty()/* || $this->getSource()->getData('base_grand_total')*/) {
                $this->_disableSubmitButton = false;
                $_submitButtonClass = '';
                break;
            }
        }
        if ($this->getOrder()->getForcedShipmentWithInvoice()) {
            $_submitLabel = Mage::helper('Mage_Sales_Helper_Data')->__('Submit Invoice and Shipment');
        } else {
            $_submitLabel = Mage::helper('Mage_Sales_Helper_Data')->__('Submit Invoice');
        }
        $this->addChild('submit_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => $_submitLabel,
            'class'     => 'save submit-button' . $_submitButtonClass,
            'onclick'   => 'disableElements(\'submit-button\');$(\'edit_form\').submit()',
            'disabled'  => $this->_disableSubmitButton
        ));

        return parent::_prepareLayout();
    }

    /**
     * Get is submit button disabled or not
     *
     * @return boolean
     */
    public function getDisableSubmitButton()
    {
        return $this->_disableSubmitButton;
    }

    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getInvoice()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getSource()
    {
        return $this->getInvoice();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return array();
    }

    /**
     * Retrieve order totalbar block data
     *
     * @return array
     */
    public function getOrderTotalbarData()
    {
        $totalbarData = array();
        $this->setPriceDataObject($this->getInvoice()->getOrder());
        $totalbarData[] = array(Mage::helper('Mage_Sales_Helper_Data')->__('Paid Amount'), $this->displayPriceAttribute('amount_paid'), false);
        $totalbarData[] = array(Mage::helper('Mage_Sales_Helper_Data')->__('Refund Amount'), $this->displayPriceAttribute('amount_refunded'), false);
        $totalbarData[] = array(Mage::helper('Mage_Sales_Helper_Data')->__('Shipping Amount'), $this->displayPriceAttribute('shipping_captured'), false);
        $totalbarData[] = array(Mage::helper('Mage_Sales_Helper_Data')->__('Shipping Refund'), $this->displayPriceAttribute('shipping_refunded'), false);
        $totalbarData[] = array(Mage::helper('Mage_Sales_Helper_Data')->__('Order Grand Total'), $this->displayPriceAttribute('grand_total'), true);

        return $totalbarData;
    }

    public function formatPrice($price)
    {
        return $this->getInvoice()->getOrder()->formatPrice($price);
    }

    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('*/*/updateQty', array('order_id'=>$this->getInvoice()->getOrderId()));
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

    public function canEditQty()
    {
        if ($this->getInvoice()->getOrder()->getPayment()->canCapture()) {
            return $this->getInvoice()->getOrder()->getPayment()->canCapturePartial();
        }
        return true;
    }

    /**
     * Check if capture operation is allowed in ACL
     * @return bool
     */
    public function isCaptureAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Sales::capture');
    }

    /**
     * Check if invoice can be captured
     * @return bool
     */
    public function canCapture()
    {
        return $this->getInvoice()->canCapture();
    }

    /**
     * Check if gateway is associated with invoice order
     * @return bool
     */
    public function isGatewayUsed()
    {
        return $this->getInvoice()->getOrder()->getPayment()->getMethodInstance()->isGateway();
    }

    public function canSendInvoiceEmail()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->canSendNewInvoiceEmail($this->getOrder()->getStore()->getId());
    }
}
