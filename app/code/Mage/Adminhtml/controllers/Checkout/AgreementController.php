<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax rule controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Checkout_AgreementController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title(__('Terms and Conditions'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Checkout_Agreement'))
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
        $agreementModel  = Mage::getModel('Mage_Checkout_Model_Agreement');

        if ($id) {
            $agreementModel->load($id);
            if (!$agreementModel->getId()) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(
                    __('This condition no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($agreementModel->getId() ? $agreementModel->getName() : __('New Condition'));

        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getAgreementData(true);
        if (!empty($data)) {
            $agreementModel->setData($data);
        }

        Mage::register('checkout_agreement', $agreementModel);

        $this->_initAction()
            ->_addBreadcrumb(
                $id ? __('Edit Condition')
                    :  __('New Condition'),
                $id ?  __('Edit Condition')
                    :  __('New Condition')
            )
            ->_addContent(
                $this->getLayout()
                    ->createBlock('Mage_Adminhtml_Block_Checkout_Agreement_Edit')
                    ->setData('action', $this->getUrl('*/*/save'))
            )
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('Mage_Checkout_Model_Agreement');
            $model->setData($postData);

            try {
                $model->save();

                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(__('The condition has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(__('Something went wrong while saving this condition.'));
            }

            Mage::getSingleton('Mage_Adminhtml_Model_Session')->setAgreementData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = Mage::getSingleton('Mage_Checkout_Model_Agreement')
            ->load($id);
        if (!$model->getId()) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(__('This condition no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();

            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(__('The condition has been deleted.'));
            $this->_redirect('*/*/');

            return;
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(__('Something went wrong  while deleting this condition.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Mage_Checkout::sales_checkoutagreement')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Checkout Conditions'), __('Checkout Terms and Conditions'))
        ;
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Checkout::checkoutagreement');
    }
}
