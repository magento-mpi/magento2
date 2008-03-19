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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert GUI admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     MKyaw Soe Lynn<mvincent@varien.com>
 */
class Mage_Adminhtml_System_Convert_OscController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Initailization of action
	 */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('adminhtml/system_convert_osc');
        return $this;
    }

    /**
     * Initialization of Osc
     *
     * @param idFieldnName string
     * @return Mage_Adminhtml_System_Convert_OscController
     */
    protected function _initOsc($idFieldName = 'id')
    {
    	$id = (int) $this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('oscommerce/oscommerce');
        if ($id) {
            $model->load($id);
        }
        
        Mage::register('current_convert_osc', $model);
        return $this;
    }
    
    /**
     * Index osc action
     */
    public function indexAction()
    {
    	$this->_initAction();
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_osc')
        );
        $this->renderLayout();
    }

    /**
     * Edit osc action
     */
    public function editAction()
    {
    	$this->_initOsc();
    	$this->loadLayout();
    	
 		$model = Mage::registry('current_convert_osc');
        $data = Mage::getSingleton('adminhtml/session')->getSystemConvertOscData(true);

        if (!empty($data)) {
            $model->addData($data);
        }
    	
        $this->_initAction();
        $this->_addBreadcrumb
             	(Mage::helper('adminhtml')->__('Edit OsCommerce Profile'),
            	 Mage::helper('adminhtml')->__('Edit OsCommerce Profile'));
        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/system_convert_osc_edit_tabs'));
                    	 
		$this->_addContent($this->getLayout()->createBlock('adminhtml/system_convert_osc_edit'));
		
        $this->renderLayout();    	
    }

    /**
     * Create new osc action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save osc action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $this->_initOsc('import_id');
            $model = Mage::registry('current_convert_osc');

            // Prepare saving data
            if (isset($data)) {
                $model->addData($data);
            }

            if (empty($data['port'])) 
            	$data['port'] = Mage_Oscommerce_Model_Oscommerce::DEFAULT_PORT;
            
            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('OsCommerce Profile was successfully saved'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSystemConvertOscData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id'=>$model->getId())));
                return;
            }
        }
        if ($this->getRequest()->getParam('continue')) {
            $this->_redirect('*/*/edit', array('id'=>$model->getId()));
        } else {
            $this->_redirect('*/*');
        }
    }
    
    public function runAction()
    {
    	$this->_initOsc();
    	$model = Mage::registry('current_convert_osc');
    	$model->importStores();
    }
    
    /**
     * Delete osc action
     */
    public function deleteAction()
    {
        $this->_initOsc();
        $model = Mage::registry('current_convert_osc');
        if ($model->getId()) {
            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('OsCommerce profile was deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/system_convert_osc');
    }    
}