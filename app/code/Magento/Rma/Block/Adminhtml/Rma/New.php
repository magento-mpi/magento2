<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Adminhtml_Rma_New extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize RMA new page. Set management buttons
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'Magento_Rma';

        parent::_construct();

        $this->_updateButton('reset', 'label', __('Cancel'));
        $this->_updateButton('reset', 'class', 'cancel');

        $orderId    = false;
        $link       = $this->getUrl('*/*/');

        if (Mage::registry('current_order') && Mage::registry('current_order')->getId()) {
            $order      = Mage::registry('current_order');
            $orderId    = $order->getId();

            $referer    = $this->getRequest()->getServer('HTTP_REFERER');

            if (strpos($referer, 'customer') !== false) {
                $link = $this->getUrl('*/customer/edit/',
                    array(
                        'id'  => $order->getCustomerId(),
                        'active_tab'=> 'orders'
                    )
                );
            }
        } else {
            return;
        }

        if (Mage::helper('Magento_Rma_Helper_Data')->canCreateRma($orderId, true)) {
            $this->_updateButton('reset', 'onclick', "setLocation('" . $link . "')");
            $this->_updateButton('save', 'label', __('Submit Returns'));
        } else {
            $this->_updateButton('reset', 'onclick', "setLocation('" . $link . "')");
            $this->_removeButton('save');
        }
        $this->_removeButton('back');
    }

    /**
     * Get header text for RMA edit page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->getLayout()->createBlock('Magento_Rma_Block_Adminhtml_Rma_Create_Header')->toHtml();
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => Mage::registry('current_order')->getId()));
    }
}
