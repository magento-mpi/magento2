<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Controller;

class Guest extends \Magento\Core\Controller\Front\Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * View all returns
     */
    public function returnsAction()
    {
        if (!$this->_objectManager->get('Magento\Rma\Helper\Data')->isEnabled()
            || !$this->_objectManager->get('Magento\Sales\Helper\Guest')->loadValidOrder()) {
            $this->_forward('noRoute');
            return;
        }
        $this->loadLayout();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs($this);
        $this->renderLayout();
    }

    /**
     * Check order view availability
     *
     * @param   \Magento\Rma\Model\Rma $rma
     * @return  bool
     */
    protected function _canViewRma($rma)
    {
        $currentOrder = $this->_coreRegistry->registry('current_order');
        if ($rma->getOrderId() && ($rma->getOrderId() === $currentOrder->getId())) {
            return true;
        }
        return false;
    }

    /**
     * View concrete rma
     */
    public function viewAction()
    {
        if (!$this->_loadValidRma()) {
            $this->_redirect('*/*/returns');
            return;
        }

        $this->loadLayout();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs($this);
        $this->getLayout()
            ->getBlock('head')
            ->setTitle(__('Return #%1', $this->_coreRegistry->registry('current_rma')->getIncrementId()));
        $this->renderLayout();
    }

    /**
     * Try to load valid rma by entity_id and register it
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadValidRma($entityId = null)
    {
        if (!$this->_objectManager->get('Magento\Rma\Helper\Data')->isEnabled() ||
            !$this->_objectManager->get('Magento\Sales\Helper\Guest')->loadValidOrder()) {
            return;
        }

        if (null === $entityId) {
            $entityId = (int) $this->getRequest()->getParam('entity_id');
        }

        if (!$entityId) {
            $this->_forward('noRoute');
            return false;
        }

        $rma = \Mage::getModel('Magento\Rma\Model\Rma')->load($entityId);

        if ($this->_canViewRma($rma)) {
            $this->_coreRegistry->register('current_rma', $rma);
            return true;
        } else {
            $this->_redirect('*/*/returns');
        }
        return false;
    }

    /**
     * Customer create new return
     */
    public function createAction()
    {
        if (!$this->_objectManager->get('Magento\Sales\Helper\Guest')->loadValidOrder()) {
            return;
        }
        $order      = $this->_coreRegistry->registry('current_order');
        $orderId    = $order->getId();
        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        $post = $this->getRequest()->getPost();
        if (($post) && !empty($post['items'])) {
            try {
                $rmaModel = \Mage::getModel('Magento\Rma\Model\Rma');
                $rmaData = array(
                    'status'                => \Magento\Rma\Model\Rma\Source\Status::STATE_PENDING,
                    'date_requested'        => \Mage::getSingleton('Magento\Core\Model\Date')->gmtDate(),
                    'order_id'              => $order->getId(),
                    'order_increment_id'    => $order->getIncrementId(),
                    'store_id'              => $order->getStoreId(),
                    'customer_id'           => $order->getCustomerId(),
                    'order_date'            => $order->getCreatedAt(),
                    'customer_name'         => $order->getCustomerName(),
                    'customer_custom_email' => $post['customer_custom_email']
                );
                $result = $rmaModel->setData($rmaData)->saveRma($post);
                if (!$result) {
                    $this->_redirectError(\Mage::getUrl('*/*/create', array('order_id'  => $orderId)));
                    return;
                }
                $result->sendNewRmaEmail();
                if (isset($post['rma_comment']) && !empty($post['rma_comment'])) {
                    \Mage::getModel('Magento\Rma\Model\Rma\Status\History')
                        ->setRmaEntityId($rmaModel->getId())
                        ->setComment($post['rma_comment'])
                        ->setIsVisibleOnFront(true)
                        ->setStatus($rmaModel->getStatus())
                        ->setCreatedAt(\Mage::getSingleton('Magento\Core\Model\Date')->gmtDate())
                        ->save();
                }
                \Mage::getSingleton('Magento\Core\Model\Session')->addSuccess(
                    __('You submitted Return #%1.', $rmaModel->getIncrementId())
                );
                $this->_redirectSuccess(\Mage::getUrl('*/*/returns'));
                return;
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Core\Model\Session')->addError(
                    __('We cannot create a new return transaction. Please try again later.')
                );
                \Mage::logException($e);
            }
        }
        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Core\Model\Session');
        $this->getLayout()->getBlock('head')->setTitle(__('Create New Return'));
        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    /**
     * Try to load valid collection of ordered items
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        if ($this->_objectManager->get('Magento\Rma\Helper\Data')->canCreateRma($orderId)) {
            return true;
        }

        $incrementId = $this->_coreRegistry->registry('current_order')->getIncrementId();
        $message = __('We cannot create a return transaction for order #%1.', $incrementId);
        \Mage::getSingleton('Magento\Core\Model\Session')->addError($message);
        $this->_redirect('sales/order/history');
        return false;
    }

    /**
     * Add RMA comment action
     */
    public function addCommentAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $response   = false;
                $comment    = $this->getRequest()->getPost('comment');
                $comment    = trim(strip_tags($comment));

                if (!empty($comment)) {
                    $result = Mage::getModel('Magento\Rma\Model\Rma\Status\History')
                        ->setRmaEntityId($this->_coreRegistry->registry('current_rma')->getEntityId())
                        ->setComment($comment)
                        ->setIsVisibleOnFront(true)
                        ->setStatus($this->_coreRegistry->registry('current_rma')->getStatus())
                        ->setCreatedAt(\Mage::getSingleton('Magento\Core\Model\Date')->gmtDate())
                        ->save();
                    $result->setStoreId($this->_coreRegistry->registry('current_rma')->getStoreId());
                    $result->sendCustomerCommentEmail();
                } else {
                    \Mage::throwException(__('Please enter a valid message.'));
                }
            } catch (\Magento\Core\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot add a message.')
                );
            }
            if (is_array($response)) {
               \Mage::getSingleton('Magento\Core\Model\Session')->addError($response['message']);
            }
            $this->_redirect('*/*/view', array('entity_id' => (int)$this->getRequest()->getParam('entity_id')));
            return;
        }
        return;
    }
    /**
     * Add Tracking Number action
     */
    public function addLabelAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    \Mage::throwException(__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = $this->getRequest()->getPost('number');
                $number    = trim(strip_tags($number));
                $carrier   = $this->getRequest()->getPost('carrier');
                $carriers  = $this->_objectManager->get('Magento\Rma\Helper\Data')
                    ->getShippingCarriers($rma->getStoreId());

                if (!isset($carriers[$carrier])) {
                    \Mage::throwException(__('Please select a valid carrier.'));
                }

                if (empty($number)) {
                    \Mage::throwException(__('Please enter a valid tracking number.'));
                }

                \Mage::getModel('Magento\Rma\Model\Shipping')
                    ->setRmaEntityId($rma->getEntityId())
                    ->setTrackNumber($number)
                    ->setCarrierCode($carrier)
                    ->setCarrierTitle($carriers[$carrier])
                    ->save();

            } catch (\Magento\Core\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot add a label.')
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => __('The wrong RMA was selected.')
            );
        }
        if (is_array($response)) {
            \Mage::getSingleton('Magento\Core\Model\Session')->setErrorMessage($response['message']);
        }

        $this->addPageLayoutHandles();
        $this->loadLayout(false)
            ->renderLayout();
        return;
    }
    /**
     * Delete Tracking Number action
     */
    public function delLabelAction()
    {
        if ($this->_loadValidRma()) {
            try {
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    \Mage::throwException(__('Shipping Labels are not allowed.'));
                }

                $response   = false;
                $number    = intval($this->getRequest()->getPost('number'));

                if (empty($number)) {
                    \Mage::throwException(__('Please enter a valid tracking number.'));
                }

                $trackingNumber = \Mage::getModel('Magento\Rma\Model\Shipping')
                    ->load($number);
                if ($trackingNumber->getRmaEntityId() !== $rma->getId()) {
                    \Mage::throwException(__('The wrong RMA was selected.'));
                }
                $trackingNumber->delete();

            } catch (\Magento\Core\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            } catch (\Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => __('We cannot delete the label.')
                );
            }
        } else {
            $response = array(
                'error'     => true,
                'message'   => __('The wrong RMA was selected.')
            );
        }
        if (is_array($response)) {
            \Mage::getSingleton('Magento\Core\Model\Session')->setErrorMessage($response['message']);
        }

        $this->addPageLayoutHandles();
        $this->loadLayout(false)
            ->renderLayout();
        return;
    }


}
