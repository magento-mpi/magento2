<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom Variables admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_System_Variable extends Magento_Adminhtml_Controller_Action
{
    /**
     * Initialize Layout and set breadcrumbs
     *
     * @return Magento_Adminhtml_Controller_System_Variable
     */
    protected function _initLayout()
    {
        $this->loadLayout()
            ->_setActiveMenu('Magento_Adminhtml::system_variable')
            ->_addBreadcrumb(__('Custom Variables'), __('Custom Variables'));
        return $this;
    }

    /**
     * Initialize Variable object
     *
     * @return Magento_Core_Model_Variable
     */
    protected function _initVariable()
    {
        $this->_title(__('Custom Variables'));

        $variableId = $this->getRequest()->getParam('variable_id', null);
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        /* @var $emailVariable Magento_Core_Model_Variable */
        $variable = Mage::getModel('Magento_Core_Model_Variable');
        if ($variableId) {
            $variable->setStoreId($storeId)
                ->load($variableId);
        }
        Mage::register('current_variable', $variable);
        return $variable;
    }

    /**
     * Index Action
     *
     */
    public function indexAction()
    {
        $this->_title(__('Custom Variables'));

        $this->_initLayout();
        $this->renderLayout();
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
        $variable = $this->_initVariable();

        $this->_title($variable->getId() ? $variable->getCode() : __('New Custom Variable'));

        $this->_initLayout()
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_System_Variable_Edit'))
            ->_addJs($this->getLayout()->createBlock('Magento_Core_Block_Template', '', array(
                'data' => array('template' => 'Magento_Adminhtml::system/variable/js.phtml')
            )))
            ->renderLayout();
    }

    /**
     * Validate Action
     *
     */
    public function validateAction()
    {
        $response = new Magento_Object(array('error' => false));
        $variable = $this->_initVariable();
        $variable->addData($this->getRequest()->getPost('variable'));
        $result = $variable->validate();
        if ($result !== true && is_string($result)) {
            $this->_getSession()->addError($result);
            $this->_initLayoutMessages('Magento_Adminhtml_Model_Session');
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
        $variable = $this->_initVariable();
        $data = $this->getRequest()->getPost('variable');
        $back = $this->getRequest()->getParam('back', false);
        if ($data) {
            $data['variable_id'] = $variable->getId();
            $variable->setData($data);
            try {
                $variable->save();
                $this->_getSession()->addSuccess(
                    __('You saved the custom variable.')
                );
                if ($back) {
                    $this->_redirect('*/*/edit', array('_current' => true, 'variable_id' => $variable->getId()));
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
        $variable = $this->_initVariable();
        if ($variable->getId()) {
            try {
                $variable->delete();
                $this->_getSession()->addSuccess(
                    __('You deleted the customer.')
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
     * WYSIWYG Plugin Action
     *
     */
    public function wysiwygPluginAction()
    {
        $customVariables = Mage::getModel('Magento_Core_Model_Variable')->getVariablesOptionArray(true);
        $storeContactVariabls = Mage::getModel('Magento_Core_Model_Source_Email_Variables')->toOptionArray(true);
        $variables = array($storeContactVariabls, $customVariables);
        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }

    /**
     * Check current user permission
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::variable');
    }
}
