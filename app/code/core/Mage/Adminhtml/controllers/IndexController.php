<?php

class Mage_Adminhtml_IndexController extends Mage_Core_Controller_Front_Action
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
        $this->getLayout()->getBlock('menu')->setActive('dashboard');
        $block = $this->getLayout()->createBlock('core/template', 'system.info')
            ->setTemplate('adminhtml/system/info.phtml');
        
        $this->getLayout()->getBlock('content')->append($block);
        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/system/left.phtml'));
        
        $this->renderLayout();
    }

    public function layoutFrameAction()
    {
        $this->_forward('index');
    }

    public function exampleAction()
    {
        $this->_outTemplate('example');
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
        $this->getResponse()->setRedirect(Mage::getUrl('adminhtml'));
    }
    
    public function globalSearchAction()
    {
        $searchModules = Mage::getConfig()->getNode("admin/search/global/collections");
        $items = array();
        if (empty($searchModules)) {
            $items[] = array('id'=>'error', 'type'=>'Error', 'name'=>'No search modules registered', 'description'=>'Please make sure that all global admin search modules are installed and activated.');
            $totalCount = 1;
        } else {
            $request = $this->getRequest()->getPost();
            foreach ($searchModules->children() as $searchConfig) {
                $className = $searchConfig->getClassName();
                $searchInstance = new $className();
                $results = $searchInstance->setStart($request['start'])->setLimit($request['limit'])->setQuery($request['query'])->load()->getResults();
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