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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom Variables for Transactional emails admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_Email_VariableController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize Layout and set breadcrumbs
     *
     * @return Mage_Adminhtml_System_Email_VariableController
     */
    protected function _initLayout()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/email_template_variable')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Transactional Emails'), Mage::helper('adminhtml')->__('Transactional Emails'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Variable'), Mage::helper('adminhtml')->__('Custom Variable'));
        return $this;
    }

    /**
     * Initialize Variable object
     *
     * @return Mage_Core_Model_Email_Variable
     */
    protected function _initEmailVariable()
    {
        $variableId = $this->getRequest()->getParam('variable_id', null);
        $storeId = $this->getRequest()->getParam('store', 0);
        /* @var $emailVariable Mage_Core_Model_Email_Variable */
        $emailVariable = Mage::getModel('core/email_variable');
        if ($variableId) {
            $emailVariable->setStoreId($storeId)
                ->load($variableId);
        }
        Mage::register('current_email_variable', $emailVariable);
        return $emailVariable;
    }

    /**
     * Index Action
     *
     */
    public function indexAction()
    {
        $this->_initLayout()
            ->_addContent($this->getLayout()->createBlock('adminhtml/system_email_variable'))
            ->renderLayout();
    }

    /**
     * New Action (forward to edit action)
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Action
     *
     */
    public function editAction()
    {
        $this->_initEmailVariable();
        $this->_initLayout()
            ->_addContent($this->getLayout()->createBlock('adminhtml/system_email_variable_edit'))
            ->_addJs($this->getLayout()->createBlock('core/template', '', array('template' => 'system/email/variable/js.phtml')))
            ->renderLayout();
    }

    /**
     * Validate Action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object(array('error' => false));
        $emailVariable = $this->_initEmailVariable();
        $emailVariable->addData($this->getRequest()->getPost('email_variable'));
        $result = $emailVariable->validate();
        if ($result !== true && is_string($result)) {
            $this->_getSession()->addError($result);
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Save Action
     *
     */
    public function saveAction()
    {
        $emailVariable = $this->_initEmailVariable();
        $data = $this->getRequest()->getPost('email_variable');
        $back = $this->getRequest()->getParam('back', false);
        if ($data) {
            $emailVariable->addData($data);
            try {
                $emailVariable->save();
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__('Custom Variable has been successfully saved.')
                );
                if ($back) {
                    $this->_redirect('*/*/edit', array('_current' => true, 'variable_id' => $emailVariable->getId()));
                } else {
                    $this->_redirect('*/*/', array());
                }
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current' => true, ));
                return;
            }
        }
        $this->_redirect('*/*/', array());
        return;
    }

    /**
     * Delete Action
     *
     */
    public function deleteAction()
    {
        $emailVariable = $this->_initEmailVariable();
        if ($emailVariable->getId()) {
            try {
                $emailVariable->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__('Custom Variable has been successfully deleted.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current' => true, ));
                return;
            }
        }
        $this->_redirect('*/*/', array());
        return;
    }

    /**
     * Check current user permission
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/email_template/variables');
    }
}