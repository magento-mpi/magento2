<?php
set_time_limit(0);
/**
 * Install wizard controller
 *
 * @package     Mage
 * @subpackage  Inastall
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_WizardController extends Mage_Core_Controller_Front_Action
{
    protected function _prepareLayout()
    {
        $this->loadLayout('front', 'install_wizard');
        $step = Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
    }
    
    public function beginAction()
    {
        $this->_prepareLayout();
        Mage::getModel('install', 'installer_filesystem')->install();
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.begin')
            ->setTemplate('install/begin.phtml')
            ->assign('messages', Mage::getSingleton('install', 'session')->getMessages(true))
            ->assign('step', Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest()));

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        
        $this->renderLayout();
    }
    
    public function licenseAction()
    {
        $this->_prepareLayout();
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.license')
            ->setTemplate('install/license.phtml')
            ->assign('step', Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest()))
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
        $this->_prepareLayout();
        
        Mage::getModel('install', 'installer_env')->install();

        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.check')
            ->setTemplate('install/check.phtml')
            ->assign('messages', Mage::getSingleton('install', 'session')->getMessages(true))
            ->assign('step', Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest()));
        
        $this->getLayout()->getBlock('content')->append($contentBlock);

        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');            
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function configAction()
    {
        $this->_prepareLayout();
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.config')
            ->setTemplate('install/config.phtml')
            ->assign('postAction', Mage::getUrl('install', array('controller'=>'wizard', 'action'=>'configPost')))
            ->assign('data', Mage::getModel('install', 'installer_config')->getFormData())
            ->assign('step', Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest()));

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');            
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function configPostAction()
    {
        if ($data = $this->getRequest()->getPost('config')) {
            Mage::getSingleton('install', 'session')->setConfigData($data);
        }
        
        $step = Mage::getSingleton('install', 'wizard')->getStepByName('config');
        $this->getResponse()->setRedirect($step->getNextUrl());
    }
    
    public function dbAction()
    {
        $this->_prepareLayout();
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.db')
            ->setTemplate('install/create_db.phtml')
            ->assign('messages', Mage::getSingleton('install', 'session')->getMessages(true))
            ->assign('postAction', Mage::getUrl('install', array('controller'=>'wizard', 'action'=>'dbPost')))
            ->assign('step', Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest()));
        
        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');            
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function dbPostAction()
    {
        $step = Mage::getSingleton('install', 'wizard')->getStepByName('db');
        if ($data = $this->getRequest()->getPost('config')) {
            try {
                Mage::getSingleton('install', 'installer_db')->checkDatabase($data);
                // If config data initialized in previos steps
                if ($configData = Mage::getSingleton('install', 'session')->getConfigData()) {
                    $data = array_merge($configData, $data);
                }
                
                Mage::getSingleton('install', 'session')->setConfigData($data);
                
                Mage::getSingleton('install', 'installer_config')->install();
            }
            catch (Exception $e){
                $this->getResponse()->setRedirect($step->getUrl());
                return false;
            }            
        }
        $this->getResponse()->setRedirect($step->getNextUrl());
    }
    
    public function administratorAction()
    {
        $this->_prepareLayout();
        Mage_Core_Resource_Setup::applyAllUpdates();
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.administrator')
            ->setTemplate('install/create_admin.phtml')
            ->assign('postAction', Mage::getUrl('install', array('controller'=>'wizard', 'action'=>'administratorPost')))
            ->assign('data', new Varien_Object())
            ->assign('step', Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest()));
        
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
        $this->_prepareLayout();
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.modules')
            ->setTemplate('install/modules.phtml')
            ->assign('step', Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest()));
        
        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');            
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
    
    public function endAction()
    {
        $this->_prepareLayout();
        
        $contentBlock = $this->getLayout()->createBlock('tpl', 'install.end')
            ->setTemplate('install/end.phtml')
            ->assign('step', Mage::getSingleton('install', 'wizard')->getStepByRequest($this->getRequest()));
            
        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install_state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
}