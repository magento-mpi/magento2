<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional buttons on order view page
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Order_View_Buttons extends Mage_Adminhtml_Block_Sales_Order_View
{
    /**
     * Add button to Shopping Cart Management etc.
     *
     * @return return
     */
    public function addButtons()
    {
        $container = $this->getParentBlock();
        if ($container instanceof Mage_Adminhtml_Block_Template && $container->getOrderId()) {
            $isReturnable = Mage::helper('Enterprise_Rma_Helper_Data')->canCreateRma($container->getOrder(), true);
            if ($isReturnable) {
                $url = Mage::getSingleton('Mage_Adminhtml_Model_Url')
                   ->getUrl('*/rma/new', array('order_id' => $container->getOrderId()));
                $order = 35;
                if (isset($this->_buttons[0]['send_notification']['sort_order'])) {
                    $order = $this->_buttons[0]['send_notification']['sort_order'] + 5;
                }
                $container->addButton('create_rma', array(
                    'label' => Mage::helper('Enterprise_Rma_Helper_Data')->__('Create RMA'),
                    'onclick' => "setLocation('" . $url . "')",
                ), 0, $order);
            }
        }
        return $this;
    }
}
