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
        return !$themeService->isCustomizationsExist();
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
            if (!$this->_isFirstEntrance()) {
                /** @var $themeService Mage_Core_Model_Theme_Service */
                $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
                $this->getLayout()->getBlock('assigned.theme.list')->setCollection(
                    $themeService->getAssignedThemeCustomizations()
                );
                $this->getLayout()->getBlock('unassigned.theme.list')->setCollection(
                    $themeService->getUnassignedThemeCustomizations()
                );
            }
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
        $page = $this->getRequest()->getParam('page', 1);
        $pageSize = $this->getRequest()
            ->getParam('page_size', Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE);

        try {
            $this->loadLayout();
            /** @var $service Mage_Core_Model_Theme_Service */
            $service = $this->_objectManager->get('Mage_Core_Model_Theme_Service');

            /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
            $collection = $service->getThemes($page, $pageSize);
            $this->getLayout()->getBlock('available.theme.list')->setCollection($collection)->setNextPage(++$page);
            $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode(
                array('content' => $this->getLayout()->getOutput())
            ));
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode(
                array('error' => $this->_helper->__('Theme list can not be loaded')))
            );
        }
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
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->_redirect('*/*/');
            return;
        }

        $storeId = (int)$this->getRequest()->getParam('store_id');
        $this->_redirectUrl($session->getPreviewUrl($storeId));
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
     * Activate preview mode for selected theme
     */
    public function previewAction()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        if (!$session->isDesignPreviewActive()) {
            $session->activateDesignPreview();
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Assign theme to list of store views
     */
    public function assignThemeToStoreAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $stores = $this->getRequest()->getParam('stores');

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');

        try {
            if (!is_numeric($themeId)) {
                throw new InvalidArgumentException('Theme id is not valid');
            }

            if ($stores === '') {
                $ids = array_keys(Mage::app()->getStores());
                $stores = array(array_shift($ids));
            }

            if (!is_array($stores) || empty($stores)) {
                throw new InvalidArgumentException('Param "stores" is not valid');
            }

            /** @var $themeService Mage_Core_Model_Theme_Service */
            $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
            $themeService->assignThemeToStores($themeId, $stores);
            $message = $coreHelper->__('Theme successfully assigned');
            $this->getResponse()->setBody($coreHelper->jsonEncode(array('success' => $message)));
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                array('error' => $this->_helper->__('Theme is not assigned')))
            );
        }
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
