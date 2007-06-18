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
        //$this->_outTemplate('frameset');
        $this->_forward('layoutFrame');
    }

    public function jsFrameAction()
    {
        $this->_outTemplate('frame/js');
    }

    public function waitFrameAction()
    {
        $this->_outTemplate('frame/wait');
    }

    public function layoutFrameAction()
    {
        $this->loadLayout('baseframe');
        
        $block = $this->getLayout()->createBlock('core/template', 'system.info')
            ->setTemplate('adminhtml/system/info.phtml');
        
        $this->getLayout()->getBlock('content')->append($block);
        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('core/template')->setTemplate('adminhtml/system/left.phtml'));
        
        $this->renderLayout();
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
}