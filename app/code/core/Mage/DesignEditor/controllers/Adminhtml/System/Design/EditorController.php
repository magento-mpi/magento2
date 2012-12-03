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
     * Display theme selector
     */
    public function indexAction()
    {
        $this->_doSelectionTheme('firstEntrance');
    }

    /**
     * Display available theme list. Only when no customized themes
     */
    public function firstEntranceAction()
    {
        $this->_doSelectionTheme('index');
    }

    /**
     * Check whether is customized themes in database
     *
     * @return bool
     */
    protected function _isFirstEntrance()
    {
        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
        return !$themeService->isPresentCustomizedThemes();
    }

    /**
     * Load layout
     *
     * @param string $forwardAction
     */
    protected function _doSelectionTheme($forwardAction)
    {
        if ($forwardAction == 'index' xor $this->_isFirstEntrance()) {
            $this->_forward($forwardAction);
            return;
        }

        try {
            $this->_title($this->__('System'))->_title($this->__('Design'))->_title($this->__('Editor'));
            $this->loadLayout();
            $this->_setActiveMenu('Mage_DesignEditor::system_design_editor');
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot load list of themes.'));
            $this->_redirectUrl($this->_getRefererUrl());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Ajax loading available themes
     */
    public function loadThemeListAction()
    {
        $page = $this->getRequest()->getParam('page', false) ?: 1;

        $this->loadLayout();

        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_objectManager->create('Mage_Core_Model_Resource_Theme_Collection');
        $collection->addAreaFilter()->setPageSize()->setCurPage($page);

        $this->getLayout()->getBlock('available.theme.list')->setCollection($collection)->setNextPage(++$page);
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode(
            array('content' => $this->getLayout()->getOutput())
        ));
    }

    /**
     * Activate the design editor in the session and redirect to the frontend of the selected store
     */
    public function launchAction()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = $this->_objectManager->get('Mage_DesignEditor_Model_Session');

        $themeId = (int)$this->getRequest()->getParam('theme_id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        try {
            $theme->load($themeId);
            if (!$theme->getId()) {
                $this->_objectManager->get('Mage_Core_Model_Logger')
                    ->throwException($this->__('The theme was not found.'));
            }
            $session->activateDesignEditor();
            $session->setThemeId($theme->getId());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('The theme was not found.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
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
     * Exit design editor
     */
    public function exitAction()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = $this->_objectManager->get('Mage_DesignEditor_Model_Session');
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
        return $this->_objectManager->get('Mage_Core_Model_Authorization')->isAllowed('Mage_DesignEditor::editor');
    }
}
