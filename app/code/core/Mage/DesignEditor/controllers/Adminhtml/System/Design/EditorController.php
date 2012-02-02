<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend controller for the design editor
 */
class Mage_DesignEditor_Adminhtml_System_Design_EditorController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Design'))->_title($this->__('Editor'));
        $this->loadLayout();
        $this->_setActiveMenu('system/design/editor');
        $this->renderLayout();
    }

    public function launchAction()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $session->activateDesignEditor();
        /* Redirect to the frontend */
        $query = Mage_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM . '=' . urlencode($session->getSessionId());
        if (!Mage::app()->isSingleStoreMode()) {
            $storeId = (int)$this->getRequest()->getParam('store_id');
            $store = Mage::app()->getStore($storeId);
            $baseUrl = $store->getBaseUrl();
            $query .= '&___store=' . urlencode($store->getCode());
        } else {
            $baseUrl = Mage::app()->getStore(true)->getBaseUrl();
        }
        $this->_redirectUrl($baseUrl . '?' . $query);
    }

    /**
     * Exit design editor
     */
    public function exitAction()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $session->deactivateDesignEditor();
        $this->loadLayout();
        $this->renderLayout();
    }
}
