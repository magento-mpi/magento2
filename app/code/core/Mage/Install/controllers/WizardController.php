<?php
/**
 * Install wizard controller
 *
 * @package     MAge
 * @subpackage  Inastall
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_WizardController extends Mage_Core_Controller_Front_Action
{
    public function beginAction()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        Mage::getModel('install', 'installer_filesystem')->install();
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.begin')
            ->setTemplate('install/begin.phtml')
            ->assign('messages', Mage::getSingleton('install', 'session')->getMessages(true))
            ->assign('step', $step);

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        
        $this->renderLayout();
    }
    
    public function licenseAction()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.license')
            ->setTemplate('install/license.phtml')
            ->assign('step', $step)
            ->assign('postAction', Mage::getUrl('install', array('controller'=>'wizard', 'action'=>'licensePost')));
            
        $this->getLayout()->getBlock('content')->append($contentBlock);
        
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function licensePostAction()
    {
        $agree = $this->getRequest()->getPost('agree');

        if ($agree && $step = Mage::getSingleton('install', 'wizard')->getStepByName('license')) {
            $this->getResponse()->setRedirect($step->getNextUrl());
        }
        else {
            $this->getResponse()->setRedirect(Mage::getUrl('install', array('controller'=>'wizard', 'action'=>'license')));
        }
    }
    
    public function checkAction()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        Mage::getModel('install', 'installer_env')->install();

        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.check')
            ->setTemplate('install/check.phtml')
            ->assign('messages', Mage::getSingleton('install', 'session')->getMessages(true))
            ->assign('step', $step);
        
        $this->getLayout()->getBlock('content')->append($contentBlock);

        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');            
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function configAction()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.config')
            ->setTemplate('install/config.phtml')
            ->assign('postAction', Mage::getUrl('install', array('controller'=>'wizard', 'action'=>'configPost')))
            ->assign('data', new Varien_Object())
            ->assign('step', $step);

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');            
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function configPostAction()
    {
        $step = Mage::getSingleton('install', 'wizard')->getStepByName('config');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }
    
    public function dbAction()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.db')
            ->setTemplate('install/create_db.phtml')
            ->assign('postAction', Mage::getUrl('install', array('controller'=>'wizard', 'action'=>'dbPost')))
            ->assign('step', $step);
        
        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');            
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function dbPostAction()
    {
        $step = Mage::getSingleton('install', 'wizard')->getStepByName('db');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }
    
    public function administratorAction()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.administrator')
            ->setTemplate('install/create_admin.phtml')
            ->assign('postAction', Mage::getUrl('install', array('controller'=>'wizard', 'action'=>'administratorPost')))
            ->assign('data', new Varien_Object())
            ->assign('step', $step);
        
        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function administratorPostAction()
    {
        $step = Mage::getSingleton('install', 'wizard')->getStepByName('administrator');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }
    
    public function modulesAction()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.modules')
            ->setTemplate('install/modules.phtml')
            ->assign('step', $step);
        
        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');            
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function endAction()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.end')
            ->setTemplate('install/end.phtml')
            ->assign('step', $step);
            
        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
}