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
namespace Magento\Adminhtml\Controller\Sales\Order\View;

class Giftmessage extends \Magento\Adminhtml\Controller\Action
{
    public function saveAction()
    {
        try {
            $this->_getGiftmessageSaveModel()
                ->setGiftmessages($this->getRequest()->getParam('giftmessage'))
                ->saveAllInOrder();
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
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
     * @return \Magento\Adminhtml\Model\Giftmessage\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_objectManager->get('Magento\Adminhtml\Model\Giftmessage\Save');
    }

}
