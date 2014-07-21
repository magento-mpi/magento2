<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class SaveNew extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Prepare RMA instance data from save request
     *
     * @param array $saveRequest
     * @return array
     */
    protected function _prepareNewRmaInstanceData(array $saveRequest)
    {
        $order = $this->_coreRegistry->registry('current_order');
        /** @var $dateModel \Magento\Framework\Stdlib\DateTime\DateTime */
        $dateModel = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
        $rmaData = array(
            'status' => \Magento\Rma\Model\Rma\Source\Status::STATE_PENDING,
            'date_requested' => $dateModel->gmtDate(),
            'order_id' => $order->getId(),
            'order_increment_id' => $order->getIncrementId(),
            'store_id' => $order->getStoreId(),
            'customer_id' => $order->getCustomerId(),
            'order_date' => $order->getCreatedAt(),
            'customer_name' => $order->getCustomerName(),
            'customer_custom_email' => !empty($saveRequest['contact_email']) ? $saveRequest['contact_email'] : ''
        );
        return $rmaData;
    }

    /**
     * Process additional RMA information (like comment, customer notification etc)
     *
     * @param array $saveRequest
     * @param \Magento\Rma\Model\Rma $rma
     * @return \Magento\Rma\Controller\Adminhtml\Rma
     */
    protected function _processNewRmaAdditionalInfo(array $saveRequest, \Magento\Rma\Model\Rma $rma)
    {
        /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
        $systemComment = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
        $systemComment->setRma($rma);
        if (isset($saveRequest['rma_confirmation']) && $saveRequest['rma_confirmation']) {
            $systemComment->sendNewRmaEmail();
        }
        $systemComment->saveSystemComment();
        if (!empty($saveRequest['comment']['comment'])) {
            $visible = isset($saveRequest['comment']['is_visible_on_front']);
            /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
            $customComment = $this->_objectManager->create('Magento\Rma\Model\Rma\Status\History');
            $customComment->setRma($rma);
            $customComment->saveComment($saveRequest['comment']['comment'], $visible, true);
        }
        return $this;
    }

    /**
     * Save new RMA request
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost() || $this->getRequest()->getParam('back', false)) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        try {
            /** @var $model \Magento\Rma\Model\Rma */
            $model = $this->_initModel();
            $saveRequest = $this->_filterRmaSaveRequest($this->getRequest()->getPost());
            $model->setData($this->_prepareNewRmaInstanceData($saveRequest));
            if (!$model->saveRma($saveRequest)) {
                throw new \Magento\Framework\Model\Exception(__('We failed to save this RMA.'));
            }
            $this->_processNewRmaAdditionalInfo($saveRequest, $model);
            $this->messageManager->addSuccess(__('You submitted the RMA request.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $errorKeys = $this->_objectManager->get('Magento\Framework\Session\Generic')->getRmaErrorKeys();
            $controllerParams = array('order_id' => $this->_coreRegistry->registry('current_order')->getId());
            if (!empty($errorKeys) && isset($errorKeys['tabs']) && $errorKeys['tabs'] == 'items_section') {
                $controllerParams['active_tab'] = 'items_section';
            }
            $this->_redirect('adminhtml/*/new', $controllerParams);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We failed to save this RMA.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->_redirect('adminhtml/*/');
    }
}
