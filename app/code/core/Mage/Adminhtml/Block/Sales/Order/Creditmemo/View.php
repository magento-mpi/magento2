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
 * Adminhtml creditmemo view
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Add & remove control buttons
     *
     */
    public function __construct()
    {
        $this->_objectId    = 'creditmemo_id';
        $this->_controller  = 'sales_order_creditmemo';
        $this->_mode        = 'view';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if (!$this->getCreditmemo()) {
            return;
        }

        if ($this->getCreditmemo()->canCancel()) {
            $this->_addButton('cancel', array(
                'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Cancel'),
                'class'     => 'delete',
                'onclick'   => 'setLocation(\''.$this->getCancelUrl().'\')'
                )
            );
        }

        if ($this->_isAllowedAction('emails')) {
            $this->addButton('send_notification', array(
                'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Send Email'),
                'onclick'   => 'confirmSetLocation(\''
                . Mage::helper('Mage_Sales_Helper_Data')->__('Are you sure you want to send Creditmemo email to customer?')
                . '\', \'' . $this->getEmailUrl() . '\')'
            ));
        }

        if ($this->getCreditmemo()->canRefund()) {
            $this->_addButton('refund', array(
                'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Refund'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getRefundUrl().'\')'
                )
            );
        }

        if ($this->getCreditmemo()->canVoid()) {
            $this->_addButton('void', array(
                'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Void'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getVoidUrl().'\')'
                )
            );
        }

        if ($this->getCreditmemo()->getId()) {
            $this->_addButton('print', array(
                'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Print'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
            );
        }
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getEmailSent()) {
            $emailSent = Mage::helper('Mage_Sales_Helper_Data')->__('the credit memo email was sent');
        }
        else {
            $emailSent = Mage::helper('Mage_Sales_Helper_Data')->__('the credit memo email is not sent');
        }
        return Mage::helper('Mage_Sales_Helper_Data')->__('Credit Memo #%1$s | %3$s | %2$s (%4$s)', $this->getCreditmemo()->getIncrementId(), $this->formatDate($this->getCreditmemo()->getCreatedAtDate(), 'medium', true), $this->getCreditmemo()->getStateName(), $emailSent);
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array(
                'order_id'  => $this->getCreditmemo() ? $this->getCreditmemo()->getOrderId() : null,
                'active_tab'=> 'order_creditmemos'
            ));
    }

    /**
     * Retrieve capture url
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->getUrl('*/*/capture', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    /**
     * Retrieve void url
     *
     * @return string
     */
    public function getVoidUrl()
    {
        return $this->getUrl('*/*/void', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    /**
     * Retrieve cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    /**
     * Retrieve email url
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl('*/*/email', array(
            'creditmemo_id' => $this->getCreditmemo()->getId(),
            'order_id'      => $this->getCreditmemo()->getOrderId()
        ));
    }

    /**
     * Retrieve print url
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'creditmemo_id' => $this->getCreditmemo()->getId()
        ));
    }

    /**
     * Update 'back' button url
     *
     * @return Mage_Adminhtml_Block_Widget_Container | Mage_Adminhtml_Block_Sales_Order_Creditmemo_View
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getCreditmemo()->getBackUrl()) {
                return $this->_updateButton(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getCreditmemo()->getBackUrl() . '\')'
                );
            }

            return $this->_updateButton(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl('*/sales_creditmemo/') . '\')'
            );
        }
        return $this;
    }

    /**
     * Check whether action is allowed
     *
     * @param string $action
     * @return bool
     */
    public function _isAllowedAction($action)
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('sales/order/actions/' . $action);
    }
}
