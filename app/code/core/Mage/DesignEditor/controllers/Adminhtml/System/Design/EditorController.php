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
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');

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
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                array('content' => $this->getLayout()->getOutput())
            ));
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                array('error' => $this->_helper->__('Theme list can not be loaded')))
            );
        }
    }

    /**
     * Activate the design editor in the session and redirect to the frontend of the selected store
     */
    public function launchAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id', $this->_getSession()->getData('theme_id'));
        $mode = (string)$this->getRequest()->getParam('mode', Mage_DesignEditor_Model_State::MODE_DESIGN);
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');

        try {
            $theme->load($themeId);
            if (!$theme->getId()) {
                throw new InvalidArgumentException($this->__('The theme was not found.'));
            }

            if (!$theme->isVirtual()) {
                $themeCustomization = $this->_getThemeCustomization($theme);
                $this->_redirect('*/*/*/', array(
                     'theme_id' => $themeCustomization->getId(),
                     'mode'     => $mode
                ));
                return;
            }

            $this->_getSession()->setData('theme_id', $theme->getId());

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

            /** @var $toolbarBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons */
            $toolbarBlock = $this->getLayout()->getBlock('design_editor_toolbar_buttons');
            $toolbarBlock->setThemeId($themeId)
                ->setMode($mode);

            /** @var $hierarchyBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_HandlesHierarchy */
            $hierarchyBlock = $this->getLayout()->getBlock('design_editor_toolbar_handles_hierarchy');
            if ($hierarchyBlock) {
                $hierarchyBlock->setHierarchy($pageTypes)
                    ->setMode($mode);
            }

            /** @var $viewOptionsBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_ViewOptions */
            $viewOptionsBlock = $this->getLayout()->getBlock('design_editor_toolbar_view_options');
            if ($viewOptionsBlock) {
                $viewOptionsBlock->setMode($mode);
            }

            /** @var $editorBlock Mage_DesignEditor_Block_Adminhtml_Editor_Container */
            $editorBlock = $this->getLayout()->getBlock('design_editor');
            if ($mode == Mage_DesignEditor_Model_State::MODE_NAVIGATION) {
                $currentUrl = $this->_getCurrentUrl();
            } else {
                $currentUrl = $this->_getCurrentHandleUrl();
            }
            $editorBlock->setFrameUrl($currentUrl);

            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('The theme was not found.'));
            $this->_redirect('*/*/');
            return;
        }
    }

    /**
     * Get current handle
     *
     * @return string
     */
    protected function _getCurrentHandleUrl()
    {
        /** @var $vdeUrlModel Mage_DesignEditor_Model_Url_Handle */
        $vdeUrlModel = $this->_objectManager->get('Mage_DesignEditor_Model_Url_Handle');
        $handle = $this->_getSession()->getData('vde_current_handle');
        if (empty($handle)) {
            $handle = 'default';
        }

        return $vdeUrlModel->getUrl('design/page/type', array('handle' => $handle));
    }

    /**
     * Get current url
     *
     * @return string
     */
    protected function _getCurrentUrl()
    {
        /** @var $vdeUrlModel Mage_DesignEditor_Model_Url_NavigationMode */
        $vdeUrlModel = $this->_objectManager->get('Mage_DesignEditor_Model_Url_NavigationMode');
        $url = $this->_getSession()->getData('vde_current_url');
        if (empty($url)) {
            $url = '';
        }

        return $vdeUrlModel->getUrl(ltrim($url, '/'));
    }

    /**
     * Assign theme to list of store views
     */
    public function assignThemeToStoreAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id', $this->_getSession()->getData('theme_id'));
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
            /** @var $themeCustomization Mage_Core_Model_Theme */
            $themeCustomization = $themeService->assignThemeToStores($themeId, $stores);
            $message = $coreHelper->__('Theme successfully assigned');
            $response = array(
                'success' => $message,
                'themeId' => $themeCustomization->getId()
            );
            $this->getResponse()->setBody($coreHelper->jsonEncode(array('success' => $message)));
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                array('error' => $this->_helper->__('Theme is not assigned')))
            );
            $response = array(
                'error'   => true,
                'message' => $this->_helper->__('Theme is not assigned')
            );
        }
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Rename title action
     */
    public function quickEditAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $themeTitle = (string)$this->getRequest()->getParam('theme_title');

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');

        try {
            /** @var $theme Mage_Core_Model_Theme */
            $theme = $this->_objectManager->get('Mage_Core_Model_Theme');
            if (!($themeId && $theme->load($themeId)->getId())) {
                throw new Mage_Core_Exception($this->__('The theme was not found.'));
            }
            if (!$theme->isVirtual()) {
                throw new Mage_Core_Exception($this->__('This theme is not editable.'));
            }
            $theme->setThemeTitle($themeTitle);
            $theme->save();
            $this->getResponse()->setBody($coreHelper->jsonEncode(array('success' => true)));
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($coreHelper->jsonEncode(array(
                'error' => true,
                'message' => $e->getMessage()
            )));
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                    array('error' => true, 'message' => $this->__('Theme is not saved')))
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
     * Save layout update
     *
     * @param int $themeId
     * @param bool $isTemporary
     */
    protected function _saveLayoutUpdate($themeId, $isTemporary = false)
    {
        $layoutUpdate = $this->getRequest()->getParam('layoutUpdate', '');
        if (!empty($layoutUpdate)) {
            $historyModel = $this->_compactHistory($layoutUpdate);

            /** @var $layoutRenderer Mage_DesignEditor_Model_History_Renderer_LayoutUpdate */
            $layoutRenderer = $this->_objectManager->get('Mage_DesignEditor_Model_History_Renderer_LayoutUpdate');
            $layoutUpdate = $historyModel->output($layoutRenderer, 'current_handle');

            /** @var $updateCollection Mage_Core_Model_Resource_Layout_Update_Collection */
            $updateCollection = $this->_objectManager->get('Mage_Core_Model_Resource_Layout_Update_Collection');
            $updateCollection->addStoreFilter(Mage_Core_Model_App::ADMIN_STORE_ID)
                ->addThemeFilter($themeId)
                ->addFieldToFilter('handle', $this->getRequest()->getParam('handle'))
                ->setOrder('sort_order');
            /** @var $layoutUpdateModel Mage_Core_Model_Layout_Update */
            $layoutUpdateModel = $updateCollection->getFirstItem();

            $sortOrder = 0;
            if ($layoutUpdateModel->getId()) {
                $sortOrder = $layoutUpdateModel->getSortOrder() + 1;
            }

            $layoutUpdateModel->setData(array(
                'store_id'     => Mage_Core_Model_App::ADMIN_STORE_ID,
                'theme_id'     => $themeId,
                'handle'       => $this->getRequest()->getParam('handle'),
                'xml'          => $layoutUpdate,
                'sort_order'   => $sortOrder,
                'is_temporary' => $isTemporary
            ));
            $layoutUpdateModel->save();
        }
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

    /**
     * Save temporary layout update
     * @throws InvalidArgumentException
     */
    public function saveTemporaryLayoutUpdateAction()
    {
        $themeId = (int)$this->_getSession()->getData('theme_id');
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');

        try {
            if (!is_numeric($themeId)) {
                throw new InvalidArgumentException('Theme id is not valid');
            }

            if ($this->getRequest()->has('layoutUpdate')) {
                $this->_saveLayoutUpdate($themeId, true);
            }
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                    array('success' => $this->__('Temporary layout update saved'))
                ));
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                    array('error' => $this->__('Temporary layout update not saved'))
                ));
        }
    }

    /**
     * Get theme customization
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme
     */
    protected function _getThemeCustomization($theme)
    {
        /** @var $service Mage_Core_Model_Theme_Service */
        $service = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
        return $service->createThemeCustomization($theme);
    }
}
