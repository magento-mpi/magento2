<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Controller\Adminhtml;

class Agreement extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\App\Action\Title
     */
    protected $_title;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\App\Action\Title $title
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\App\Action\Title $title
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_title = $title;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->_title->add(__('Terms and Conditions'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Magento\Checkout\Block\Adminhtml\Agreement'))
            ->renderLayout();
        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title->add(__('Terms and Conditions'));

        $id  = $this->getRequest()->getParam('id');
        $agreementModel  = $this->_objectManager->create('Magento\Checkout\Model\Agreement');

        if ($id) {
            $agreementModel->load($id);
            if (!$agreementModel->getId()) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(
                    __('This condition no longer exists.')
                );
                $this->_redirect('checkout/*/');
                return;
            }
        }

        $this->_title->add($agreementModel->getId() ? $agreementModel->getName() : __('New Condition'));

        $data = $this->_objectManager->get('Magento\Adminhtml\Model\Session')->getAgreementData(true);
        if (!empty($data)) {
            $agreementModel->setData($data);
        }

        $this->_coreRegistry->register('checkout_agreement', $agreementModel);

        $this->_initAction()
            ->_addBreadcrumb(
                $id ? __('Edit Condition') :  __('New Condition'),
                $id ?  __('Edit Condition') :  __('New Condition')
            )
            ->_addContent(
                $this->getLayout()
                    ->createBlock('Magento\Checkout\Block\Adminhtml\Agreement\Edit')
                    ->setData('action', $this->getUrl('checkout/*/save'))
            )
            ->renderLayout();
    }

    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {
            $model = $this->_objectManager->get('Magento\Checkout\Model\Agreement');
            $model->setData($postData);

            try {
                $model->save();

                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(__('The condition has been saved.'));
                $this->_redirect('checkout/*/');

                return;
            } catch (\Magento\Core\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong while saving this condition.'));
            }

            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->setAgreementData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->_objectManager->get('Magento\Checkout\Model\Agreement')
            ->load($id);
        if (!$model->getId()) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('This condition no longer exists.'));
            $this->_redirect('checkout/*/');
            return;
        }

        try {
            $model->delete();
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addSuccess(__('The condition has been deleted.'));
            $this->_redirect('checkout/*/');
            return;
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong  while deleting this condition.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Initialize action
     *
     * @return \Magento\Backend\App\Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Checkout::sales_checkoutagreement')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Checkout Conditions'), __('Checkout Terms and Conditions'));
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Checkout::checkoutagreement');
    }
}
