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
 * Invoice view  comments form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Comments_View extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Please correct the parent block for this block.'));
        }
        $this->setEntity($this->getParentBlock()->getSource());
        parent::_beforeToHtml();
    }

    /**
     * Prepare child blocks
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items
     */
    protected function _prepareLayout()
    {
        $this->addChild('submit_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'id'      => 'submit_comment_button',
            'label'   => Mage::helper('Mage_Sales_Helper_Data')->__('Submit Comment'),
            'class'   => 'save'
        ));


        return parent::_prepareLayout();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment',array('id'=>$this->getEntity()->getId()));
    }

    public function canSendCommentEmail()
    {
        $helper = Mage::helper('Mage_Sales_Helper_Data');
        switch ($this->getParentType()) {
            case 'invoice':
                return $helper->canSendInvoiceCommentEmail($this->getEntity()->getOrder()->getStore()->getId());
            case 'shipment':
                return $helper->canSendShipmentCommentEmail($this->getEntity()->getOrder()->getStore()->getId());
            case 'creditmemo':
                return $helper->canSendCreditmemoCommentEmail($this->getEntity()->getOrder()->getStore()->getId());
        }

        return true;
    }
}
