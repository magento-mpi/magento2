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
class Magento_Adminhtml_Controller_Tax_Rule extends Magento_Adminhtml_Controller_Action
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
        $ruleModel  = $this->_objectManager->create('Magento_Tax_Model_Calculation_Rule');
        if ($taxRuleId) {
            $ruleModel->load($taxRuleId);
            if (!$ruleModel->getId()) {
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->unsRuleData();
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError(__('This rule no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_objectManager->get('Magento_Adminhtml_Model_Session')->getRuleData(true);
        if (!empty($data)) {
            $ruleModel->setData($data);
        }

        $this->_title($ruleModel->getId() ? sprintf("%s", $ruleModel->getCode()) : __('New Tax Rule'));

        $this->_coreRegistry->register('tax_rule', $ruleModel);

        $this->_initAction()
            ->_addBreadcrumb($taxRuleId ? __('Edit Rule') :  __('New Rule'), $taxRuleId ?  __('Edit Rule') :  __('New Rule'))
            ->renderLayout();
    }

    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {

            $ruleModel = $this->_objectManager->get('Magento_Tax_Model_Calculation_Rule');
            $ruleModel->setData($postData);

            try {
                $ruleModel->save();

                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addSuccess(__('The tax rule has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('rule' => $ruleModel->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (Magento_Core_Exception $e) {
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError(__('Something went wrong saving this tax rule.'));
            }

            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->setRuleData($postData);
            $this->_redirectReferer();
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('*/tax_rule'));
    }

    public function deleteAction()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule');
        $ruleModel = $this->_objectManager->get('Magento_Tax_Model_Calculation_Rule')
            ->load($ruleId);
        if (!$ruleModel->getId()) {
            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError(__('This rule no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $ruleModel->delete();

            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addSuccess(__('The tax rule has been deleted.'));
            $this->_redirect('*/*/');

            return;
        } catch (Magento_Core_Exception $e) {
            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Adminhtml_Model_Session')->addError(__('Something went wrong deleting this tax rule.'));
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
            ->_setActiveMenu('Magento_Tax::sales_tax_rules')
            ->_addBreadcrumb(__('Tax'), __('Tax'))
            ->_addBreadcrumb(__('Tax Rules'), __('Tax Rules'));
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Tax::manage_tax');
    }
}
