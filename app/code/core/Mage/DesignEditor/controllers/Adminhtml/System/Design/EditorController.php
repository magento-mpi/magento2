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
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        try {
            $theme->load($themeId);
            if (!$theme->getId()) {
                Mage::throwException($this->__('The theme was not found.'));
            }
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

        $this->_redirectUrl('*/*/');
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

        /** @var $hierarchyBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_HandlesHierarchy */
        $hierarchyBlock = $this->getLayout()->getBlock('design_editor_toolbar_handles_hierarchy');
        if ($hierarchyBlock) {
            $hierarchyBlock->setHierarchy($pageTypes);
        }

        /** @var $editorBlock Mage_DesignEditor_Block_Adminhtml_Editor_Container */
        $editorBlock = $this->getLayout()->getBlock('design_editor');

        /** @var $vdeUrlModel Mage_DesignEditor_Model_Url_NavigationMode */
        $vdeUrlModel = $this->_objectManager->get('Mage_DesignEditor_Model_Url_NavigationMode');
        $editorBlock->setFrameUrl($vdeUrlModel->getUrl('design/page/type', array('handle' => 'default')));

        $this->renderLayout();
    }

    /**
     * Activate preview mode for selected theme
     */
    public function previewAction()
    {
    //        /** @var $session Mage_DesignEditor_Model_Session */
    //        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
    //        if (!$session->isDesignPreviewActive()) {
    //            $session->activateDesignPreview();
    //        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Assign theme to list of store views
     * //TODO used until VDE router implemented
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

            //TODO used until we find a way to convert array to JSON on JS side
            $defaultStore = -1;
            $emptyStores = -2;
            if ($stores == $defaultStore) {
                $ids = array_keys(Mage::app()->getStores());
                $stores = array(array_shift($ids));
            } elseif ($stores == $emptyStores) {
                $stores = array();
            }

            if (!is_array($stores)) {
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

    /**
     * Compact history
     *
     * @param array $historyData
     * @return Mage_DesignEditor_Model_History
     */
    protected function _compactHistory($historyData)
    {
        /** @var $historyModel Mage_DesignEditor_Model_History */
        $historyModel = Mage::getModel('Mage_DesignEditor_Model_History');
        /** @var $historyCompactModel Mage_DesignEditor_Model_History_Compact */
        $historyCompactModel = Mage::getModel('Mage_DesignEditor_Model_History_Compact');
        /** @var $collection Mage_DesignEditor_Model_Change_Collection */
        $collection = $historyModel->setChanges($historyData)->getChanges();
        $historyCompactModel->compact($collection);
        return $historyModel;
    }

    /**
     * Get layout xml
     */
    public function getLayoutUpdateAction()
    {
        $historyData = Mage::app()->getRequest()->getPost('historyData');
        if (!$historyData) {
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
                array(Mage_Core_Model_Message::ERROR => array($this->__('Invalid post data')))
            ));
            return;
        }

        try {
            $historyModel = $this->_compactHistory($historyData);
            /** @var $layoutRenderer Mage_DesignEditor_Model_History_Renderer_LayoutUpdate */
            $layoutRenderer = Mage::getModel('Mage_DesignEditor_Model_History_Renderer_LayoutUpdate');
            $layoutUpdate = $historyModel->output($layoutRenderer);
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                Mage_Core_Model_Message::SUCCESS => array($layoutUpdate)
            )));
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
                array(Mage_Core_Model_Message::ERROR => array($e->getMessage()))
            ));
        }
    }
}
