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
namespace Magento\Adminhtml\Controller\Tax;

class Rule extends \Magento\Adminhtml\Controller\Action
{
    public function indexAction()
    {
        $this->_title(__('Tax Rules'));

        $this->_initAction();
        $this->renderLayout();

        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title(__('Tax Rules'));

        $taxRuleId  = $this->getRequest()->getParam('rule');
        $ruleModel  = \Mage::getModel('Magento\Tax\Model\Calculation\Rule');
        if ($taxRuleId) {
            $ruleModel->load($taxRuleId);
            if (!$ruleModel->getId()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->unsRuleData();
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This rule no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getRuleData(true);
        if (!empty($data)) {
            $ruleModel->setData($data);
        }

        $this->_title($ruleModel->getId() ? sprintf("%s", $ruleModel->getCode()) : __('New Tax Rule'));

        \Mage::register('tax_rule', $ruleModel);

        $this->_initAction()
            ->_addBreadcrumb($taxRuleId ? __('Edit Rule') :  __('New Rule'), $taxRuleId ?  __('Edit Rule') :  __('New Rule'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {

            $ruleModel = \Mage::getSingleton('Magento\Tax\Model\Calculation\Rule');
            $ruleModel->setData($postData);

            try {
                $ruleModel->save();

                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('The tax rule has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('rule' => $ruleModel->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            }
            catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
            }
            catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong saving this tax rule.'));
            }

            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setRuleData($postData);
            $this->_redirectReferer();
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('*/tax_rule'));
    }

    public function deleteAction()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule');
        $ruleModel = \Mage::getSingleton('Magento\Tax\Model\Calculation\Rule')
            ->load($ruleId);
        if (!$ruleModel->getId()) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This rule no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $ruleModel->delete();

            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('The tax rule has been deleted.'));
            $this->_redirect('*/*/');

            return;
        }
        catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
        }
        catch (\Exception $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('Something went wrong deleting this tax rule.'));
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
            ->_setActiveMenu('Magento_Tax::sales_tax_rules')
            ->_addBreadcrumb(__('Tax'), __('Tax'))
            ->_addBreadcrumb(__('Tax Rules'), __('Tax Rules'))
        ;
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Tax::manage_tax');
    }
}
