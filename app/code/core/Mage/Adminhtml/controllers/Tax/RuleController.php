<?php
class Mage_Adminhtml_Tax_RuleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('sales');
        $this->_addBreadcrumb(__('Tax rules'), __('Tax rules title'));

        $this->_addTabs();

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/tax_rule_toolbar_add', 'tax_rule_toolbar')
            ->assign('createUrl', Mage::getUrl('adminhtml/tax_rule/add'))
            ->assign('header', __('Tax rules'))
        );
        $this->_addContent($this->getLayout()->createBlock('adminhtml/tax_rule_grid', 'tax_rule_grid'));

        $this->renderLayout();
    }

    public function addAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('sales');
        $this->_addBreadcrumb(__('Tax rules'), __('Tax rules title'), Mage::getUrl('adminhtml/tax'));
        $this->_addBreadcrumb(__('New tax rule'), __('New tax rule title'));

        $this->_addTabs();

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/tax_rule_toolbar_save')
            ->assign('header', __('New tax rule'))
        );
        $this->_addContent($this->getLayout()->createBlock('adminhtml/tax_rule_form_add'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $ruleObject = new Varien_Object();
        $ruleObject->setTaxRuleId($this->getRequest()->getParam('rule_id', null));
        $ruleObject->setTaxCustomerClassId($this->getRequest()->getParam('customer_tax_class'));
        $ruleObject->setTaxProductClassId($this->getRequest()->getParam('product_tax_class'));
        $ruleObject->setTaxRateId($this->getRequest()->getParam('rate_type'));

        Mage::getSingleton('tax/rule')->save($ruleObject);
        $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('sales');
        $this->_addBreadcrumb(__('Tax rules'), __('Tax rules title'), Mage::getUrl('adminhtml/tax'));
        $this->_addBreadcrumb(__('Edit tax rule'), __('Edit tax rate rule'));

        $this->_addTabs();

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/tax_rule_toolbar_save')
            ->assign('header', __('Edit tax rule'))
        );
        $this->_addContent($this->getLayout()->createBlock('adminhtml/tax_rule_form_add'));

        $this->renderLayout();
    }

    public function deleteAction()
    {
        $ruleObject = new Varien_Object();
        $ruleObject->setTaxRuleId($this->getRequest()->getParam('rule'));
        Mage::getSingleton('tax/rule')->delete($ruleObject);
        $this->getResponse()->setRedirect(Mage::getUrl("*/*/"));
    }

    protected function _addTabs($tabId='tax_rule')
    {
        $tabs = $this->getLayout()->createBlock('adminhtml/tax_tabs')
            ->setActiveTab($tabId);
        $this->_addLeft($tabs);
    }
}