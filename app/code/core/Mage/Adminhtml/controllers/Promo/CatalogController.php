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
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('catalogrule/rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This rule does not longer exist'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        
        Mage::register('current_promo_catalog_rule', $model);

        $block = $this->getLayout()->createBlock('adminhtml/promo_catalog_edit')
            ->setData('action', Mage::getUrl('adminhtml/promo_catalog/save'));
        
        $this->_initAction();
        
        $this->getLayout()->getBlock('root')->setCanLoadRulesJs(true);
        
        $this
            ->_addBreadcrumb($id ? __('Edit Rule') : __('New Rule'), $id ? __('Edit Rule') : __('New Rule'))
            ->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('adminhtml/promo_catalog_edit_tabs'))
            ->renderLayout();
        
    }
   
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('catalogrule/rule');
//            if ($id = $this->getRequest()->getParam('page_id')) {
//                $model->load($id);
//                if ($id != $model->getId()) {
//                    Mage::getSingleton('adminhtml/session')->addError('The page you are trying to save no longer exists');
//                    Mage::getSingleton('adminhtml/session')->setPageData($data);
//                    $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
//                    return;
//                }
//            }
            $data['conditions'] = $data['rule']['conditions'];
            $data['actions'] = $data['rule']['actions'];
            unset($data['rule']);
            
            if (!empty($data['auto_apply'])) {
	            $autoApply = true;
	            unset($data['auto_apply']);
            } else {
            	$autoApply = false;
            }
            
            $model->loadPost($data);
            Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Rule was saved succesfully'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                if ($autoApply) {
                	$this->_forward('applyRules');
                } else {
                	$this->_redirect('*/*/');
                }
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('catalogrule/rule');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Rule was deleted succesfully'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(__('Unable to find a page to delete'));
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $type = str_replace('-', '/', $this->getRequest()->getParam('type'));
        
        $model = Mage::getModel($type)->setId($id)->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'));
            
        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);        
    }
    
    public function newActionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $type = str_replace('-', '/', $this->getRequest()->getParam('type'));
        
        $model = Mage::getModel($type)->setId($id)->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'));
            
        if ($model instanceof Mage_Rule_Model_Action_Abstract) {
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
    
    public function applyRulesAction()
    {
        try {
            $resource = Mage::getResourceSingleton('catalogrule/rule');
            $resource->applyAllRulesForDateRange($resource->formatDate(mktime(0,0,0)));
            Mage::getSingleton('adminhtml/session')->addSuccess(__('Rules were applied successfully'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(__('Unable to apply rules'));
            throw $e;
        }

        $this->_redirect('*/*');
    }
}