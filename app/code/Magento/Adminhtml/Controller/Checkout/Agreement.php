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
 * Tax rule controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Checkout_Agreement extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->_title(__('Terms and Conditions'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Checkout_Agreement'))
            ->renderLayout();
        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title(__('Terms and Conditions'));

        $id  = $this->getRequest()->getParam('id');
        $agreementModel  = $this->_objectManager->create('Magento_Checkout_Model_Agreement');

        if ($id) {
            $agreementModel->load($id);
            if (!$agreementModel->getId()) {
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError(
                    __('This condition no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($agreementModel->getId() ? $agreementModel->getName() : __('New Condition'));

        $data = $this->_objectManager->get('Magento_Adminhtml_Model_Session')->getAgreementData(true);
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
                    ->createBlock('Magento_Adminhtml_Block_Checkout_Agreement_Edit')
                    ->setData('action', $this->getUrl('*/*/save'))
            )
            ->renderLayout();
    }

    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {
            $model = $this->_objectManager->get('Magento_Checkout_Model_Agreement');
            $model->setData($postData);

            try {
                $model->save();

                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addSuccess(__('The condition has been saved.'));
                $this->_redirect('*/*/');

                return;
            } catch (Magento_Core_Exception $e) {
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError(__('Something went wrong while saving this condition.'));
            }

            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->setAgreementData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->_objectManager->get('Magento_Checkout_Model_Agreement')
            ->load($id);
        if (!$model->getId()) {
            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError(__('This condition no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addSuccess(__('The condition has been deleted.'));
            $this->_redirect('*/*/');
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError(__('Something went wrong  while deleting this condition.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Initialize action
     *
     * @return Magento_Adminhtml_Controller_Action
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
