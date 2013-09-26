<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml sales order view gift messages controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Sales_Order_View_Giftmessage extends Magento_Adminhtml_Controller_Action
{
    public function saveAction()
    {
        try {
            $this->_getGiftmessageSaveModel()
                ->setGiftmessages($this->getRequest()->getParam('giftmessage'))
                ->saveAllInOrder();
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(__('Something went wrong while saving the gift message.'));
        }

        if($this->getRequest()->getParam('type')=='order_item') {
            $this->getResponse()->setBody(
                 $this->_getGiftmessageSaveModel()->getSaved() ? 'YES' : 'NO'
            );
        } else {
            $this->getResponse()->setBody(
                __('The gift message has been saved.')
            );
        }
    }

    /**
     * Retrieve gift message save model
     *
     * @return Magento_Adminhtml_Model_Giftmessage_Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_objectManager->get('Magento_Adminhtml_Model_Giftmessage_Save');
    }

}
