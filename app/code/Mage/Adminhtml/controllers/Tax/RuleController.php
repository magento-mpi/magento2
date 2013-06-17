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
class Mage_Adminhtml_Tax_RuleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Tax Rules'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('Mage_Adminhtml_Block_Tax_Rule'))
            ->renderLayout();
        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Tax Rules'));

        $taxRuleId  = $this->getRequest()->getParam('rule');
        $ruleModel  = Mage::getModel('Mage_Tax_Model_Calculation_Rule');
        if ($taxRuleId) {
            $ruleModel->load($taxRuleId);
            if (!$ruleModel->getId()) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->unsRuleData();
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Tax_Helper_Data')->__('This rule no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getRuleData(true);
        if (!empty($data)) {
            $ruleModel->setData($data);
        }

        $this->_title($ruleModel->getId() ? sprintf("%s", $ruleModel->getCode()) : $this->__('New Tax Rule'));

        Mage::register('tax_rule', $ruleModel);

        $this->_initAction()
            ->_addBreadcrumb($taxRuleId ? Mage::helper('Mage_Tax_Helper_Data')->__('Edit Rule') :  Mage::helper('Mage_Tax_Helper_Data')->__('New Rule'), $taxRuleId ?  Mage::helper('Mage_Tax_Helper_Data')->__('Edit Rule') :  Mage::helper('Mage_Tax_Helper_Data')->__('New Rule'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {

            $ruleModel = Mage::getSingleton('Mage_Tax_Model_Calculation_Rule');
            $ruleModel->setData($postData);

            try {
                $ruleModel->save();

                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Mage_Tax_Helper_Data')->__('The tax rule has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('rule' => $ruleModel->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Tax_Helper_Data')->__('Something went wrong saving this tax rule.'));
            }

            Mage::getSingleton('Mage_Adminhtml_Model_Session')->setRuleData($postData);
            $this->_redirectReferer();
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('*/tax_rule'));
    }

    public function deleteAction()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule');
        $ruleModel = Mage::getSingleton('Mage_Tax_Model_Calculation_Rule')
            ->load($ruleId);
        if (!$ruleModel->getId()) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Tax_Helper_Data')->__('This rule no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $ruleModel->delete();

            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess(Mage::helper('Mage_Tax_Helper_Data')->__('The tax rule has been deleted.'));
            $this->_redirect('*/*/');

            return;
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError(Mage::helper('Mage_Tax_Helper_Data')->__('Something went wrong deleting this tax rule.'));
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
            ->_setActiveMenu('Mage_Tax::sales_tax_rules')
            ->_addBreadcrumb(Mage::helper('Mage_Tax_Helper_Data')->__('Tax'), Mage::helper('Mage_Tax_Helper_Data')->__('Tax'))
            ->_addBreadcrumb(Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rules'), Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rules'))
        ;
        return $this;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Tax::manage_tax');
    }
}
