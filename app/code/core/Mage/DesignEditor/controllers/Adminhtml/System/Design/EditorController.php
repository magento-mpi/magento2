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
    /**
     * Display the design editor launcher page
     */
    public function indexAction()
    {
        try {
            $this->_title($this->__('System'))->_title($this->__('Design'))->_title($this->__('Editor'));
            $this->loadLayout();
            $this->_setActiveMenu('Mage_DesignEditor::system_design_editor');
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot load list of themes.'));
            $this->_redirectUrl($this->_getRefererUrl());
            Mage::logException($e);
        }
    }

    /**
     * Activate the design editor in the session and redirect to the frontend of the selected store
     */
    public function launchAction()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');

        $themeId = (int)$this->getRequest()->getParam('theme_id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::getModel('Mage_Core_Model_Theme');
        try {
            $theme->load($themeId);
            if (!$theme->getId()) {
                Mage::throwException($this->__('The theme was not found.'));
            }
            $session->activateDesignEditor();
            $session->setThemeId($theme->getId());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('The theme was not found.'));
            Mage::logException($e);
            $this->_redirect('*/*/');
            return;
        }

        /* Redirect to the frontend */
        $query = array(Mage_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM => urlencode($session->getSessionId()));
        $storeId = (int)$this->getRequest()->getParam('store_id');
        if (!Mage::app()->isSingleStoreMode() && $storeId) {
            $storeId = (int)$this->getRequest()->getParam('store_id');
            $params = array('_store' => $storeId);
            $store = Mage::app()->getStore($storeId);
            $query['___store'] = urlencode($store->getCode());
        }
        $params['_nosid'] = true;
        $params['_query'] = $query;
        $this->_redirectUrl(Mage::getUrl('/', $params));
    }

    /**
     * @TODO: temporary action, code from this action will be moved to launch action in MAGETWO-5573
     */
    public function runAction()
    {
        /** @var $eventDispatcher Mage_Core_Model_Event_Manager */
        $eventDispatcher = $this->_objectManager->get('Mage_Core_Model_Event_Manager');
        $eventDispatcher->dispatch('design_editor_activate');

        $customLayoutParams = array('area' => Mage_Core_Model_App_Area::AREA_FRONTEND);

        /** @var $customFrontLayout Mage_Core_Model_Layout_Merge */
        $customFrontLayout = $this->_objectManager->create('Mage_Core_Model_Layout_Merge',
            array('arguments' => $customLayoutParams)
        );
        $pageTypes = $customFrontLayout->getPageHandlesHierarchy();

        $this->_title($this->__('System'))->_title($this->__('Design'))->_title($this->__('Editor'));
        $this->loadLayout();
        $this->_setActiveMenu('Mage_DesignEditor::system_design_editor');

        /** @var $hierarchyBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_HandlesHierarchy */
        $hierarchyBlock = $this->getLayout()->getBlock('design_editor_toolbar_handles_hierarchy');
        if ($hierarchyBlock) {
            $hierarchyBlock->setHierarchy($pageTypes);
        }

        /** @var $editorBlock Mage_DesignEditor_Block_Adminhtml_Editor_Container */
        $editorBlock = $this->getLayout()->getBlock('design_editor');

        /** @var $vdeUrlModel Mage_DesignEditor_Model_Url */
        $vdeUrlModel = $this->_objectManager->get('Mage_DesignEditor_Model_Url');
        $editorBlock->setFrameUrl($vdeUrlModel->getUrl('design/page/type', array('handle' => 'default')));

        $this->renderLayout();
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

    /**
     * Whether the current user has enough permissions to execute an action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_DesignEditor::editor');
    }
}
