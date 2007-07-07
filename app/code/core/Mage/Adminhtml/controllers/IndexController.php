<?php

class Mage_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _outTemplate($tplName, $data=array())
    {
        $block = $this->getLayout()->createBlock('core/template')->setTemplate("adminhtml/$tplName.phtml");
        foreach ($data as $index=>$value) {
        	$block->assign($index, $value);
        }
        $this->getResponse()->setBody($block->toHtml());
    }

    public function indexAction()
    {
        $this->loadLayout('baseframe');
        #$this->_setActiveMenu('dashboard');
        $block = $this->getLayout()->createBlock('core/template', 'system.info')
            ->setTemplate('adminhtml/system/info.phtml');
        
        $this->_addContent($block);
        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/system/left.phtml'));
        
        $this->renderLayout();
    }
    
    public function loginAction()
    {
        $data = array(
            'username'=>$this->getRequest()->getParam('username')
        );
        $this->_outTemplate('login', $data);
    }
    
    public function logoutAction()
    {
        $auth = Mage::getSingleton('admin/session')->unsetAll();
        $this->_redirect('adminhtml');
    }
    
    public function globalSearchAction()
    {
        $searchModules = Mage::getSingleton('admin/config')->getNode("admin/search/global/collections");
        $items = array();
        if (empty($searchModules)) {
            $items[] = array('id'=>'error', 'type'=>'Error', 'name'=>'No search modules registered', 'description'=>'Please make sure that all global admin search modules are installed and activated.');
            $totalCount = 1;
        } else {
            $start = $this->getRequest()->getParam('start', 1);
            $limit = $this->getRequest()->getParam('limit', 10);
            $query = $this->getRequest()->getParam('query', '');
            foreach ($searchModules->children() as $searchConfig) {
                $className = $searchConfig->getClassName();
                if (empty($className)) {
                    continue;
                }
                $searchInstance = new $className();
                $results = $searchInstance->setStart($start)->setLimit($limit)->setQuery($query)->load()->getResults();
                $items = array_merge_recursive($items, $results);
            }
            $totalCount = sizeof($items);
        }

        $block = $this->getLayout()->createBlock('core/template')
            ->setTemplate('adminhtml/system/autocomplete.phtml')
            ->assign('items', $items);
        
        $this->getResponse()->setBody($block->toHtml());
    }
}