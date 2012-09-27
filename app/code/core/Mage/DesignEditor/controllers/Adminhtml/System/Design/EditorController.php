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
        /** @var $model Mage_Core_Model_Theme */
        $model = Mage::getModel('Mage_Core_Model_Theme');
        try {
            $themes = $model->getCollection();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $themes = array();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot load list of themes.'));
            Mage::logException($e);
            $themes = array();
        }

        $this->_title($this->__('System'))->_title($this->__('Design'))->_title($this->__('Editor'));
        $this->loadLayout();
        $this->_setActiveMenu('Mage_DesignEditor::system_design_editor');
        $this->getLayout()->getBlock('design_editor_theme_list')->setThemes($themes);
        $this->renderLayout();
    }

    /**
     * Activate the design editor in the session and redirect to the frontend of the selected store
     */
    public function launchAction()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');

        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $skin = $this->getRequest()->get('theme_skin');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::getModel('Mage_Core_Model_Theme');
        try {
            $theme->load($themeId);
            if (!$theme->getId()) {
                Mage::throwException($this->__('The theme was not found.'));
            }
            $session->activateDesignEditor();
            $session->setThemeId($theme->getId());
            $session->setSkin($skin);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('The theme or skin was not found.'));
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
        $params['_query'] = $query;
        $this->_redirectUrl(Mage::getUrl('/', $params));
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
