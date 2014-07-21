<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

use \Magento\Backend\App\Action;

class AddressSave extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * Save order address
     *
     * @return void
     */
    public function execute()
    {
        $addressId = $this->getRequest()->getParam('address_id');
        $address = $this->_objectManager->create('Magento\Sales\Model\Order\Address')->load($addressId);
        $data = $this->getRequest()->getPost();
        if ($data && $address->getId()) {
            $address->addData($data);
            try {
                $address->save();
                $this->messageManager->addSuccess(__('You updated the order address.'));
                $this->_redirect('sales/*/view', array('order_id' => $address->getParentId()));
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong updating the order address.'));
            }
            $this->_redirect('sales/*/address', array('address_id' => $address->getId()));
        } else {
            $this->_redirect('sales/*/');
        }
    }
}
