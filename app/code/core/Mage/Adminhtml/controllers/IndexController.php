<?php

class Mage_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _outTemplate($tplName, $data=array())
    {
        $this->_initLayoutMessages('adminhtml/session');
        $block = $this->getLayout()->createBlock('core/template')->setTemplate("$tplName.phtml");
        foreach ($data as $index=>$value) {
        	$block->assign($index, $value);
        }
        $this->getResponse()->setBody($block->toHtml());
    }

    public function indexAction()
    {
        $this->_redirect('*/sales_order');
        return;

        $this->loadLayout('baseframe');
        #$this->_setActiveMenu('dashboard');
        $block = $this->getLayout()->createBlock('core/template', 'system.info')
            ->setTemplate('system/info.phtml');

        $this->_addContent($block);
//        $this->getLayout()->getBlock('left')
//            ->append($this->getLayout()->createBlock('core/template')->setTemplate('system/left.phtml'));

        $this->renderLayout();
    }

    public function loginAction()
    {
        $loginData = $this->getRequest()->getParam('login');
        $data = array();

        if( is_array($loginData) && array_key_exists('username', $loginData) ) {
            $data['username'] = $loginData['username'];
        } else {
            $data['username'] = null;
        }
        $this->_outTemplate('login', $data);
    }

    public function logoutAction()
    {
        $auth = Mage::getSingleton('admin/session')->unsetAll();
        Mage::getSingleton('adminhtml/session')->addSuccess(__('You successfully logged out.'));
        $this->_redirect('*');
    }

    public function globalSearchAction()
    {
        $searchModules = Mage::getConfig()->getNode("adminhtml/global_search");
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
            ->setTemplate('system/autocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }

    public function exampleAction()
    {
        $this->_outTemplate('example');
    }

    protected function _isAllowed()
    {
    	/*if ( $this->getRequest()->getActionName() == 'login' && ! Mage::getSingleton('admin/session')->isAllowed('admin') ) {
    		Mage::getSingleton('adminhtml/session')->addError(__('You have not enought permissions to login.'));
    		$request = Mage::registry('controller')->getRequest();

    	} else {
    		return Mage::getSingleton('admin/session')->isAllowed('admin');
    	}
    	*/
    	return true;
    }

}