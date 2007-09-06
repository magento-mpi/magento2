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
 * Installation wizard controller
 */
class Mage_Install_WizardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve installer object
     *
     * @return Mage_Install_Model_Installer
     */
    protected function _getInstaller()
    {
        return Mage::getSingleton('install/installer');
    }
    
    /**
     * Retrieve wizard
     *
     * @return Mage_Install_Model_Wizard
     */
    protected function _getWizard()
    {
        return Mage::getSingleton('install/wizard');
    }

    /**
     * Prepare layout
     *
     * @return unknown
     */
    protected function _prepareLayout()
    {
        $this->loadLayout('install_wizard');
        $step = $this->_getWizard()->getStepByRequest($this->getRequest());
        if ($step) {
            $step->setActive(true);
        }
        
        $leftBlock = $this->getLayout()->createBlock('install/state', 'install.state');
        $this->getLayout()->getBlock('left')->append($leftBlock);
        return $this;
    }
    
    /**
     * Checking installation status
     *
     * @return unknown
     */
    protected function _checkIfInstalled()
    {
        if ($this->_getInstaller()->isApplicationInstalled()) {
            $this->getResponse()->setRedirect(Mage::getBaseUrl());
        }
        return $this;
    }
    
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_forward('begin');
    }
    
    /**
     * Begin installation action
     */
    public function beginAction()
    {
        $this->_checkIfInstalled();

        $this->setFlag('','no-beforeGenerateLayoutBlocksDispatch', true);
        $this->setFlag('','no-postDispatch', true);

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/begin', 'install.begin')
        );
        
        $this->renderLayout();
    }
    
    /**
     * Process begin step POST data
     */
    public function beginPostAction()
    {
        $this->_checkIfInstalled();
        
        $agree = $this->getRequest()->getPost('agree');
        if ($agree && $step = $this->_getWizard()->getStepByName('begin')) {
            $this->getResponse()->setRedirect($step->getNextUrl());
        }
        else {
            $this->_redirect('install');
        }
    }
    
    /**
     * Configuration data installation
     */
    public function configAction()
    {
        $this->_checkIfInstalled();
        $this->_getInstaller()->checkServer();

    	$this->setFlag('','no-beforeGenerateLayoutBlocksDispatch', true);
    	$this->setFlag('','no-postDispatch', true);

    	$this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/config', 'install.config')
        );
        
        $this->renderLayout();
    }
    
    /**
     * Process configuration POST data
     */
    public function configPostAction()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('config');

        if ($data = $this->getRequest()->getPost('config')) {
            try {
                $this->_getInstaller()->installConfig($data);
    	        $this->_redirect('*/*/installDb');
    	        return $this;
            }
            catch (Exception $e){
                Mage::getSingleton('install/session')->setConfigData($data);
            }
        }
        $this->getResponse()->setRedirect($step->getUrl());
    }
    
    /**
     * Install DB
     */
    public function installDbAction()
    {
        $this->_checkIfInstalled();
        $step = $this->_getWizard()->getStepByName('config');
        try {
            $this->_getInstaller()->installDb();
            /**
             * Clear session config data
             */
            Mage::getSingleton('install/session')->getConfigData(true);
            $this->getResponse()->setRedirect($step->getNextUrl());
        }
        catch (Exception $e){
            Mage::getSingleton('install/session')->addError($e->getMessage());
            $this->getResponse()->setRedirect($step->getUrl());
        }
    }
    
    /**
     * Install admininstrator account
     */
    public function administratorAction()
    {
        $this->_checkIfInstalled();

        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/admin', 'install.administrator')
        );
        $this->renderLayout();
    }
    
    /**
     * Process administrator instalation POST data
     */
    public function administratorPostAction()
    {
        $this->_checkIfInstalled();

        $step = Mage::getSingleton('install/wizard')->getStepByName('administrator');
        $adminData      = $this->getRequest()->getPost('admin');
        $encryptionKey  = $this->getRequest()->getPost('encryption_key');

        try {
            $this->_getInstaller()->createAdministrator($adminData)
                ->installEnryptionKey($encryptionKey);
        }
        catch (Exception $e){
            Mage::getSingleton('install/session')->addError($e->getMessage());
            $this->getResponse()->setRedirect($step->getUrl());
            return false;
        }
        $this->getResponse()->setRedirect($step->getNextUrl());
    }
    
    /**
     * End installation
     */
    public function endAction()
    {
        $this->_checkIfInstalled();
        $this->_getInstaller()->finish();
        
        $this->_prepareLayout();
        $this->_initLayoutMessages('install/session');
        
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('install/end', 'install.end')
        );
        $this->renderLayout();
    }
    
    /**
     * Host validation response
     */
    public function checkHostAction()
    {
        $this->getResponse()->setBody(Mage_Install_Model_Installer::INSTALLER_HOST_RESPONSE);
    }
}
