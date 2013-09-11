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
namespace Magento\Adminhtml\Controller\Checkout;

class Agreement extends \Magento\Adminhtml\Controller\Action
{
    public function indexAction()
    {
        $this->_title(__('Terms and Conditions'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Checkout\Agreement'))
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
        $agreementModel  = \Mage::getModel('Magento\Checkout\Model\Agreement');

        if ($id) {
            $agreementModel->load($id);
            if (!$agreementModel->getId()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                    __('This condition no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($agreementModel->getId() ? $agreementModel->getName() : __('New Condition'));

        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getAgreementData(true);
        if (!empty($data)) {
            $agreementModel->setData($data);
        }

        \Mage::register('checkout_agreement', $agreementModel);

        $this->_initAction()
            ->_addBreadcrumb(
                $id ? __('Edit Condition')
                    :  __('New Condition'),
                $id ?  __('Edit Condition')
                    :  __('New Condition')
            )
            ->_addContent(
                $this->getLayout()
                    ->createBlock('Magento\Adminhtml\Block\Checkout\Agreement\Edit')
                    ->setData('action', $this->getUrl('*/*/save'))
            )
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = \Mage::getSingleton('Magento\Checkout\Model\Agreement');
            $model->setData($postData);

            try {
                $model->save();

                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('The condition has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            }
            catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong while saving this condition.'));
            }

            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setAgreementData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = \Mage::getSingleton('Magento\Checkout\Model\Agreement')
            ->load($id);
        if (!$model->getId()) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This condition no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();

            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('The condition has been deleted.'));
            $this->_redirect('*/*/');

            return;
        }
        catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
        }
        catch (\Exception $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong  while deleting this condition.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Initialize action
     *
     * @return \Magento\Adminhtml\Controller\Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Checkout::sales_checkoutagreement')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Checkout Conditions'), __('Checkout Terms and Conditions'))
        ;
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Checkout::checkoutagreement');
    }
}
