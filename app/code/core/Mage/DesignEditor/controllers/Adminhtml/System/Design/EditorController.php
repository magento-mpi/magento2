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
        $query = array(
            Mage_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM => $session->getSessionId(),
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $storeId = (int)$this->getRequest()->getParam('store_id');
            $query['___store'] = Mage::app()->getStore($storeId)->getCode();
        }
        $this->_redirect('cms/index/index', array('_query' => $query));
    }
}
