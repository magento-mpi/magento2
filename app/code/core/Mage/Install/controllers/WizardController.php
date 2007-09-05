<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Install wizard controller
 */
class Mage_Install_WizardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Prepare layout
     *
     * @return unknown
     */
    protected function _prepareLayout()
    {
        $this->loadLayout('install_wizard');
        $step = Mage::getSingleton('install/wizard')->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        return $this;
    }
    
    /**
     * Checking installation status
     *
     * @return unknown
     */
    protected function _checkIfInstalled()
    {
        if (Mage::getSingleton('install/installer')->isApplicationInstalled()) {
            $this->getResponse()->setRedirect(Mage::getBaseUrl());
        }
        return $this;
    }
    
    public function indexAction()
    {
        $this->_forward('begin');
    }

    public function beginAction()
    {
        $this->_checkIfInstalled();

        $this->setFlag('','no-beforeGenerateLayoutBlocksDispatch', true);
        $this->setFlag('','no-postDispatch', true);

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');

        $contentBlock = $this->getLayout()->createBlock('core/template', 'install.begin')
            ->setTemplate('install/begin.phtml')
            ->assign('languages', Mage::getSingleton('install/config')->getLanguages())
            ->assign('step', Mage::getSingleton('install/wizard')->getStepByRequest($this->getRequest()))
            ->assign('postAction', Mage::getUrl('install/wizard/beginPost'));

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install/state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);

        $this->renderLayout();
    }

    public function beginPostAction()
    {
        $this->_checkIfInstalled();
        $agree = $this->getRequest()->getPost('agree');
        if ($agree && $step = Mage::getSingleton('install/wizard')->getStepByName('begin')) {
            $this->getResponse()->setRedirect($step->getNextUrl());
        }
        else {
            $this->_redirect('install');
        }
    }

    public function configAction()
    {
        $this->_checkIfInstalled();

    	$this->setFlag('','no-beforeGenerateLayoutBlocksDispatch', true);
    	$this->setFlag('','no-postDispatch', true);
        if (! Mage::getSingleton('install/session')->getFsEnvErrors(true)) {
            try {
                Mage::getModel('install/installer_filesystem')->install();
                Mage::getModel('install/installer_env')->install();
            } catch (Exception $e) {
                Mage::getSingleton('install/session')->addError('<br />Please set required permissions before clicking Continue');
            }
        }

        $data = Mage::getSingleton('install/session')->getConfigData(true);
        if (empty($data)) {
            $data = Mage::getModel('install/installer_config')->getFormData();
        }
        else {
            $data = new Varien_Object($data);
        }

    	$this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        $contentBlock = $this->getLayout()->createBlock('core/template', 'install.config')
            ->setTemplate('install/config.phtml')
            ->assign('postAction', Mage::getUrl('install/wizard/configPost'))
            ->assign('data', $data)
            ->assign('step', Mage::getSingleton('install/wizard')->getStepByRequest($this->getRequest()));

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install/state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }

    public function configPostAction()
    {
        set_time_limit(0);
        
        $this->_checkIfInstalled();
    
        $step = Mage::getSingleton('install/wizard')->getStepByName('config');

        if ($data = $this->getRequest()->getPost('config')) {
            $isError = false;
            try {
                Mage::getSingleton('install/session')->setConfigData($data);
                try {
                    Mage::getModel('install/installer_filesystem')->install();
                    Mage::getModel('install/installer_env')->install();
                } catch (Exception $e) {
                    Mage::getSingleton('install/session')->setFsEnvErrors(true);
                    Mage::getSingleton('install/session')->addError('<br />Please set required permissions before clicking Continue');
                    $isError = true;
                }
                
                $data['db_active'] = true;
                Mage::getSingleton('install/session')->setConfigData($data);
                Mage::getSingleton('install/installer_db')->checkDatabase($data);
                if ($isError) {
                    throw new Exception();
                }
                Mage::getSingleton('install/installer_config')->install();
                //Mage_Core_Model_Resource_Setup::applyAllUpdates();
            }
            catch (Exception $e){
                $this->getResponse()->setRedirect($step->getUrl());
                return false;
            }

            $step = Mage::getSingleton('install/wizard')->getStepByName('config');
	        $this->getResponse()->setRedirect($step->getNextUrl());
	        return true;
        }
        $this->getResponse()->setRedirect($step->getUrl());
    }

    public function administratorAction()
    {
        $this->_checkIfInstalled();

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        Mage_Core_Model_Resource_Setup::applyAllUpdates();
        $contentBlock = $this->getLayout()->createBlock('core/template', 'install.administrator')
            ->setTemplate('install/create_admin.phtml')
            ->assign('postAction', Mage::getUrl('install/wizard/administratorPost'))
            ->assign('data', new Varien_Object())
            ->assign('step', Mage::getSingleton('install/wizard')->getStepByRequest($this->getRequest()));

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install/state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }

    public function administratorPostAction()
    {
        $this->_checkIfInstalled();

        $step = Mage::getSingleton('install/wizard')->getStepByName('administrator');
        $data = $this->getRequest()->getPost();
        $encryptionKey = $data['encryption_key'];
        unset($data['encryption_key']);
        try {
            $user = Mage::getModel('admin/user')->load(1)->addData($data);
            $user->save();
            // Add this user to Administrators role /////
            $administratorsRoleId = 1;
            Mage::getModel("permissions/user")->setRoleId($administratorsRoleId)->setUserId($user->getId())->setFirstname($user->getFirstname())->add();
            /////////////////////////////////////////////
            Mage::getSingleton('install/installer_config')->setEncryptionKey($encryptionKey)->setInstalled();
            Mage::getSingleton('install/session')->setEncryptKey(Mage::getSingleton('install/installer_config')->getEncryptionKey());
        }
        catch (Exception $e){
            Mage::getSingleton('install/session')->addMessage(
                Mage::getModel('core/message')->error($e->getMessage())
            );
            $this->getResponse()->setRedirect($step->getUrl());
            return false;
        }
        $this->getResponse()->setRedirect($step->getNextUrl());
    }

    public function modulesAction()
    {
        $this->_checkIfInstalled();

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        $contentBlock = $this->getLayout()->createBlock('core/template', 'install.modules')
            ->setTemplate('install/modules.phtml')
            ->assign('step', Mage::getSingleton('install/wizard')->getStepByRequest($this->getRequest()));

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install/state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }

    public function endAction()
    {
        Mage::getSingleton('install/session')->getConfigData(true);
        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        
        $key = Mage::getSingleton('install/session')->getEncryptKey(true);
        $contentBlock = $this->getLayout()->createBlock('core/template', 'install.end')
            ->setTemplate('install/end.phtml')
            ->assign('encrypt_key', $key)
            ->assign('step', Mage::getSingleton('install/wizard')->getStepByRequest($this->getRequest()));

        $this->getLayout()->getBlock('content')->append($contentBlock);
        $leftBlock = $this->getLayout()->createBlock('install/state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        $this->renderLayout();
    }
}
