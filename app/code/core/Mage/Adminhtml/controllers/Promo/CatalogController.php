<?php

class Mage_Adminhtml_Promo_CatalogController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('promo/catalog')
            ->_addBreadcrumb(__('Promotions'), __('Promotions'))
        ;
        return $this;
    }
    
    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Catalog'), __('Catalog'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/promo_catalog'))
            ->renderLayout();
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $id = $this->getRequest()->getParam('rule_id');
        $model = Mage::getModel('catalogrule/rule');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This rule does not longer exist'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getCurrentPromoCatalogRuleData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        
        Mage::register('current_promo_catalog_rule', $model);

        $block = $this->getLayout()->createBlock('adminhtml/promo_catalog_edit')
            ->setPostActionUrl(Mage::getUrl('adminhtml/promo_catalog/save'));
            
        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit Rule') : __('New Rule'), $id ? __('Edit Rule') : __('New Rule'))
            ->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('adminhtml/promo_catalog_edit_tabs'))
            ->renderLayout();
        
    }
}