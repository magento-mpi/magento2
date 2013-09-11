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
 * Order status management controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Sales\Order;

class Status extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Initialize status model based on status code in request
     *
     * @return \Magento\Sales\Model\Order\Status | false
     */
    protected function _initStatus()
    {
        $statusCode = $this->getRequest()->getParam('status');
        if ($statusCode) {
            $status = \Mage::getModel('\Magento\Sales\Model\Order\Status')->load($statusCode);
        } else {
            $status = false;
        }
        return $status;
    }

    /**
     * Statuses grid page
     */
    public function indexAction()
    {
        $this->_title(__('Order Status'));
        $this->loadLayout()->_setActiveMenu('Magento_Sales::system_order_statuses')->renderLayout();
    }

    /**
     * New status form
     */
    public function newAction()
    {
        $data = $this->_getSession()->getFormData(true);
        if ($data) {
            $status = \Mage::getModel('\Magento\Sales\Model\Order\Status')
                ->setData($data);
            \Mage::register('current_status', $status);
        }
        $this->_title(__('Order Status'))->_title(__('Create New Order Status'));
        $this->loadLayout()
                ->_setActiveMenu('Magento_Sales::system_order_statuses')
                ->renderLayout();
    }

    /**
     * Editing existing status form
     */
    public function editAction()
    {
        $status = $this->_initStatus();
        if ($status) {
            \Mage::register('current_status', $status);
            $this->_title(__('Order Status'))->_title(__('Edit Order Status'));
            $this->loadLayout()
                ->_setActiveMenu('Magento_Sales::system_order_statuses')
                ->renderLayout();
        } else {
            $this->_getSession()->addError(
                __('We can\'t find this order status.')
            );
            $this->_redirect('*/');
        }
    }

    /**
     * Save status form processing
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        $isNew = $this->getRequest()->getParam('is_new');
        if ($data) {

            $statusCode = $this->getRequest()->getParam('status');

            //filter tags in labels/status
            /** @var $helper \Magento\Adminhtml\Helper\Data */
            $helper = \Mage::helper('Magento\Adminhtml\Helper\Data');
            if ($isNew) {
                $statusCode = $data['status'] = $helper->stripTags($data['status']);
            }
            $data['label'] = $helper->stripTags($data['label']);
            foreach ($data['store_labels'] as &$label) {
                $label = $helper->stripTags($label);
            }

            $status = \Mage::getModel('\Magento\Sales\Model\Order\Status')
                    ->load($statusCode);
            // check if status exist
            if ($isNew && $status->getStatus()) {
                $this->_getSession()->addError(
                    __('We found another order status with the same order status code.')
                );
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/new');
                return;
            }

            $status->setData($data)
                    ->setStatus($statusCode);
            try {
                $status->save();
                $this->_getSession()->addSuccess(__('You have saved the order status.'));
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    __('We couldn\'t add your order status because something went wrong saving.')
                );
            }
            $this->_getSession()->setFormData($data);
            if ($isNew) {
                $this->_redirect('*/*/new');
            } else {
                $this->_redirect('*/*/edit', array('status' => $this->getRequest()->getParam('status')));
            }
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Assign status to state form
     */
    public function assignAction()
    {
        $this->_title(__('Order Status'))->_title(__('Assign Order Status to State'));
        $this->loadLayout()
            ->_setActiveMenu('Magento_Sales::system_order_statuses')
            ->renderLayout();
    }

    /**
     * Save status assignment to state
     */
    public function assignPostAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $state  = $this->getRequest()->getParam('state');
            $isDefault = $this->getRequest()->getParam('is_default');
            $status = $this->_initStatus();
            if ($status && $status->getStatus()) {
                try {
                    $status->assignState($state, $isDefault);
                    $this->_getSession()->addSuccess(__('You have assigned the order status.'));
                    $this->_redirect('*/*/');
                    return;
                } catch (\Magento\Core\Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->_getSession()->addException(
                        $e,
                        __('An error occurred while assigning order status. Status has not been assigned.')
                    );
                }
            } else {
                $this->_getSession()->addError(__('We can\'t find this order status.'));
            }
            $this->_redirect('*/*/assign');
            return;
        }
        $this->_redirect('*/*/');
    }

    public function unassignAction()
    {
        $state  = $this->getRequest()->getParam('state');
        $status = $this->_initStatus();
        if ($status) {
            try {
                $status->unassignState($state);
                $this->_getSession()->addSuccess(__('You have unassigned the order status.'));
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    __('Something went wrong while we were unassigning the order.')
                );
            }
        } else {
            $this->_getSession()->addError(__('We can\'t find this order status.'));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::order_statuses');
    }
}
