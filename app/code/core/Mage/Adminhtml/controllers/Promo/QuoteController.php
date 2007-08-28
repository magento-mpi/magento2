<?php

class Mage_Adminhtml_Promo_QuoteController extends Mage_Adminhtml_Controller_Action
{
	protected function _initRule()
	{
		Mage::register('current_promo_quote_rule', Mage::getModel('salesrule/rule'));
        if ($id = (int) $this->getRequest()->getParam('id')) {
            Mage::registry('current_promo_quote_rule')
                ->load($id);
        }
	}

    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('promo/quote')
            ->_addBreadcrumb(__('Promotions'), __('Promotions'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Catalog'), __('Catalog'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/promo_quote'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('salesrule/rule');

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

        Mage::register('current_promo_quote_rule', $model);

        $block = $this->getLayout()->createBlock('adminhtml/promo_quote_edit')
            ->setData('action', Mage::getUrl('*/*/save'));

        $this->_initAction();

        $this->getLayout()->getBlock('root')->setCanLoadRulesJs(true);

        $this
            ->_addBreadcrumb($id ? __('Edit Rule') : __('New Rule'), $id ? __('Edit Rule') : __('New Rule'))
            ->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('adminhtml/promo_quote_edit_tabs'))
            ->_addJs($this->getLayout()->createBlock('core/template')->setTemplate('promo/quote/js.phtml'))
            ->renderLayout();

    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('salesrule/rule');

            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
                if ($id != $model->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError('The page you are trying to save no longer exists');
                    Mage::getSingleton('adminhtml/session')->setPageData($data);
                    $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
                    return;
                }
            }
            if (isset($data['rule']['conditions'])) {
            	$data['conditions'] = $data['rule']['conditions'];
            }
            if (isset($data['rule']['actions'])) {
            	$data['actions'] = $data['rule']['actions'];
            }
            unset($data['rule']);

            $model->loadPost($data);
            Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Rule was saved succesfully'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                $this->_redirect('*/*/');
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
                $model = Mage::getModel('salesrule/rule');
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
            ->setRule(Mage::getModel('salesrule/rule'));

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
            ->setRule(Mage::getModel('salesrule/rule'));

        if ($model instanceof Mage_Rule_Model_Action_Abstract) {
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function applyRulesAction()
    {
        $this->_initAction();

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->_initRule();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/promo_quote_edit_tab_product')->toHtml()
        );
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('promo/quote');
    }

}