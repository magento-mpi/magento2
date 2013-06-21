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
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Adminhtml_System_Design_EditorController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Display the design editor launcher page
     */
    public function indexAction()
    {
        if (!$this->_resolveForwarding()) {
            $this->_renderStoreDesigner();
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
            $collection = $service->getPhysicalThemes($page, $pageSize);

            /** @var $availableThemeBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Available */
            $availableThemeBlock =  $this->getLayout()->getBlock('available.theme.list');
            $availableThemeBlock->setCollection($collection)->setNextPage(++$page);
            $availableThemeBlock->setIsFirstEntrance($this->_isFirstEntrance());
            $availableThemeBlock->setHasThemeAssigned($this->_hasThemeAssigned());

            $response = array('content' => $this->getLayout()->getOutput());
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $response = array('error' => $this->_helper->__('Sorry, but we can\'t load the theme list.'));
        }
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Activate the design editor in the session and redirect to the frontend of the selected store
     */
    public function launchAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $mode = (string)$this->getRequest()->getParam('mode', Mage_DesignEditor_Model_State::MODE_NAVIGATION);
        try {
            /** @var Mage_DesignEditor_Model_Theme_Context $themeContext */
            $themeContext = $this->_objectManager->get('Mage_DesignEditor_Model_Theme_Context');
            $themeContext->setEditableThemeById($themeId);
            $launchedTheme = $themeContext->getEditableTheme();
            if ($launchedTheme->isPhysical()) {
                $lunchedTheme = $this->_getThemeCustomization($launchedTheme);
                $this->_redirect($this->getUrl('*/*/*', array('theme_id' => $lunchedTheme->getId())));
                return;
            }
            $editableTheme = $themeContext->getStagingTheme();

            $this->_eventManager->dispatch('design_editor_activate');

            $this->_setTitle();
            $this->loadLayout();

            $this->_configureToolbarBlocks($launchedTheme, $editableTheme, $mode); //top panel
            $this->_configureToolsBlocks($editableTheme, $mode); //bottom panel
            $this->_configureEditorBlock($editableTheme, $mode); //editor container

            /** @var $storeViewBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_StoreView */
            $storeViewBlock = $this->getLayout()->getBlock('theme.selector.storeview');
            $storeViewBlock->setData(array(
                'actionOnAssign' => 'none',
                'theme_id'       => $launchedTheme->getId()
            ));

            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Sorry, there was an unknown error.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->_redirect('*/*/');
            return;
        }
    }

    /**
     * Assign theme to list of store views
     */
    public function assignThemeToStoreAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $stores = $this->getRequest()->getParam('stores');
        $reportToSession = (bool)$this->getRequest()->getParam('reportToSession');

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');

        $hadThemeAssigned = $this->_hasThemeAssigned();

        try {
            $theme = $this->_loadThemeById($themeId);

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
            $themeCustomization = $themeService->reassignThemeToStores($theme->getId(), $stores);

            /** @var $storeManager Mage_Core_Model_StoreManager */
            $storeManager = $this->_objectManager->get('Mage_Core_Model_StoreManager');
            if ($storeManager->isSingleStoreMode()) {
                $themeService->assignThemeToDefaultScope($themeCustomization->getId());
            }

            $successMessage = $hadThemeAssigned
                ? $this->__('You assigned a new theme to your store view.')
                : $this->__('You assigned a theme to your live store.');
            if ($reportToSession) {
                $this->_getSession()->addSuccess($successMessage);
            }
            $response = array(
                'message' => $successMessage,
                'themeId' => $themeCustomization->getId()
            );
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($coreHelper->jsonEncode(
                array('error' => $this->_helper->__('This theme is not assigned.'))
            ));
            $response = array(
                'error'   => true,
                'message' => $this->_helper->__('This theme is not assigned.')
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
            $theme = $this->_loadThemeById($themeId);
            if (!$theme->isEditable()) {
                throw new Mage_Core_Exception($this->__('Sorry, but you can\'t edit theme "%s".',
                    $theme->getThemeTitle()));
            }
            $theme->setThemeTitle($themeTitle);
            $theme->save();
            $response = array('success' => true);
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $response = array('error' => true, 'message' => $this->__('This theme is not saved.'));
        }
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Display available theme list. Only when no customized themes
     */
    public function firstEntranceAction()
    {
        if (!$this->_resolveForwarding()) {
            $this->_renderStoreDesigner();
        }
    }

    /**
     * Apply changes from 'staging' theme to 'virtual' theme
     */
    public function saveAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');

        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');

        /** @var Mage_DesignEditor_Model_Theme_Context $themeContext */
        $themeContext = $this->_objectManager->get('Mage_DesignEditor_Model_Theme_Context');
        $themeContext->setEditableThemeById($themeId);
        try {
            $themeContext->copyChanges();
            if ($themeService->isThemeAssignedToStore($themeContext->getEditableTheme())) {
                $message = $this->__('You updated your live store.');
            } else {
                $message = $this->__('You saved updates to this theme.');
            }
            $response = array('message' =>  $message);
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $response = array('error' => true, 'message' => $this->_helper->__('Sorry, there was an unknown error.'));
        }

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Duplicate theme action
     */
    public function duplicateAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
        /** @var $themeCopy Mage_Core_Model_Theme */
        $themeCopy = $this->_objectManager->create('Mage_Core_Model_Theme');
        /** @var $copyService Mage_Core_Model_Theme_CopyService */
        $copyService = $this->_objectManager->get('Mage_Core_Model_Theme_CopyService');
        try {
            $theme = $this->_loadThemeById($themeId);
            if (!$theme->isVirtual()) {
                throw new Mage_Core_Exception($this->__('Sorry, but you can\'t edit theme "%s".',
                    $theme->getThemeTitle()));
            }
            $themeCopy->setData($theme->getData());
            $themeCopy->setId(null)->setThemeTitle($coreHelper->__('Copy of [%s]', $theme->getThemeTitle()));
            $themeCopy->getThemeImage()->createPreviewImageCopy();
            $themeCopy->save();
            $copyService->copy($theme, $themeCopy);
            $this->_getSession()->addSuccess(
                $this->__('You saved a duplicate copy of this theme in “My Customizations.”')
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->_getSession()->addError($this->__('You cannot duplicate this theme.'));
        }
        $this->_redirectUrl($this->_getRefererUrl());
    }

    /**
     * Revert 'staging' theme to the state of 'physical' or 'virtual'
     *
     * @throws Mage_Core_Exception
     */
    public function revertAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $revertTo = $this->getRequest()->getParam('revert_to');

        $virtualTheme = $this->_loadThemeById($themeId);
        if (!$virtualTheme->isVirtual()) {
            throw new Mage_Core_Exception($this->_helper->__('Theme "%s" is not editable.', $virtualTheme->getId()));
        }

        try {
            /** @var $copyService Mage_Core_Model_Theme_CopyService */
            $copyService = $this->_objectManager->get('Mage_Core_Model_Theme_CopyService');
            $stagingTheme = $virtualTheme->getDomainModel(Mage_Core_Model_Theme::TYPE_VIRTUAL)->getStagingTheme();
            switch ($revertTo) {
                case 'last_saved':
                    $copyService->copy($virtualTheme, $stagingTheme);
                    $message = $this->_helper->__('Theme "%s" reverted to last saved state',
                        $virtualTheme->getThemeTitle()
                    );
                    break;

                case 'physical':
                    $physicalTheme = $virtualTheme->getDomainModel(Mage_Core_Model_Theme::TYPE_VIRTUAL)
                        ->getPhysicalTheme();
                    $copyService->copy($physicalTheme, $stagingTheme);
                    $message = $this->_helper->__('Theme "%s" reverted to last default state',
                        $virtualTheme->getThemeTitle()
                    );
                    break;

                default:
                    throw new Magento_Exception('Invalid revert mode "%s"', $revertTo);
            }
            $response = array('message' => $message);
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $response = array('error' => true, 'message' => $this->_helper->__('Unknown error'));
        }
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = $this->_objectManager->get('Mage_Core_Helper_Data');
        $this->getResponse()->setBody($coreHelper->jsonEncode($response));
    }

    /**
     * Set page title
     */
    protected function _setTitle()
    {
        $this->_title($this->__('Store Designer'));
    }

    /**
     * Load theme by id
     *
     * @param int $themeId
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    protected function _loadThemeById($themeId)
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        if (!$themeId || !$theme->load($themeId)->getId()) {
            throw new Mage_Core_Exception($this->__('We can\'t find this theme.'));
        }
        return $theme;
    }

    /**
     * Whether the current user has enough permissions to execute an action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_DesignEditor::editor');
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

    /**
     * Pass data to the Tools panel blocks that is needed it for rendering
     *
     * @param Mage_Core_Model_Theme $theme
     * @param string $mode
     * @return Mage_DesignEditor_Adminhtml_System_Design_EditorController
     */
    protected function _configureToolsBlocks($theme, $mode)
    {
        /** @var $toolsBlock Mage_DesignEditor_Block_Adminhtml_Editor_Tools */
        $toolsBlock = $this->getLayout()->getBlock('design_editor_tools');
        if ($toolsBlock) {
            $toolsBlock->setMode($mode);
        }

        /** @var $customTabBlock Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom */
        $customTabBlock = $this->getLayout()->getBlock('design_editor_tools_code_custom');
        if ($customTabBlock) {
            $theme->setCustomization($this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css'));
            $customTabBlock->setTheme($theme);
        }

        /** @var $customTabBlock Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom */
        $customTabBlock = $this->getLayout()->getBlock('design_editor_tools_code_custom');
        if ($customTabBlock) {
            $theme->setCustomization($this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css'));
            $customTabBlock->setTheme($theme);
        }

        /** @var $cssTabBlock Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Css */
        $cssTabBlock = $this->getLayout()->getBlock('design_editor_tools_code_css');
        if ($cssTabBlock) {
            /** @var $helper Mage_Core_Helper_Theme */
            $helper = $this->_objectManager->get('Mage_Core_Helper_Theme');
            $cssFiles = $helper->getGroupedCssFiles($theme);
            $cssTabBlock->setCssFiles($cssFiles)
                ->setThemeId($theme->getId());
        }

        /** @var $jsTabBlock Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js */
        $jsTabBlock = $this->getLayout()->getBlock('design_editor_tools_code_js');
        if ($jsTabBlock) {
            /** @var $jsFileModel Mage_Core_Model_Theme_Customization_Files_Js */
            $jsFileModel = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
            $theme->setCustomization($jsFileModel);

            $jsTabBlock->setTheme($theme);
        }

        $blocks = array(
            'design_editor_tools_code_image_sizing',
            'design_editor_tools_quick-styles_header',
            'design_editor_tools_quick-styles_backgrounds',
            'design_editor_tools_quick-styles_buttons',
            'design_editor_tools_quick-styles_tips',
            'design_editor_tools_quick-styles_fonts',
        );
        foreach ($blocks as $blockName) {
            /** @var $block Mage_Core_Block_Abstract */
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                $block->setTheme($theme);
            }
        }

        return $this;
    }

    /**
     * Pass data to the Toolbar panel blocks that is needed for rendering
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Theme $editableTheme
     * @param string $mode
     * @return Mage_DesignEditor_Adminhtml_System_Design_EditorController
     */
    protected function _configureToolbarBlocks($theme, $editableTheme, $mode)
    {
        /** @var $toolbarBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons */
        $toolbarBlock = $this->getLayout()->getBlock('design_editor_toolbar_buttons');
        $toolbarBlock->setThemeId($editableTheme->getId())->setVirtualThemeId($theme->getId())
            ->setMode($mode);

        /** @var $saveButtonBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Save */
        $saveButtonBlock = $this->getLayout()->getBlock('design_editor_toolbar_buttons_save');
        if ($saveButtonBlock) {
            $saveButtonBlock->setTheme($theme)->setMode($mode)->setHasThemeAssigned($this->_hasThemeAssigned());
        }
        /** @var $saveButtonBlock Mage_DesignEditor_Block_Adminhtml_Editor_Toolbar_Buttons_Edit */
        $editButtonBlock = $this->getLayout()->getBlock('design_editor_toolbar_buttons_edit');
        if ($editButtonBlock) {
            $editButtonBlock->setTheme($editableTheme);
        }

        return $this;
    }

    /**
     * Set to iframe block selected mode and theme
     *
     * @param Mage_Core_Model_Theme $editableTheme
     * @param string $mode
     * @return Mage_DesignEditor_Adminhtml_System_Design_EditorController
     */
    protected function _configureEditorBlock($editableTheme, $mode)
    {
        /** @var $editorBlock Mage_DesignEditor_Block_Adminhtml_Editor_Container */
        $editorBlock = $this->getLayout()->getBlock('design_editor');
        $currentUrl = $this->_getCurrentUrl($editableTheme->getId(), $mode);
        $editorBlock->setFrameUrl($currentUrl);
        $editorBlock->setTheme($editableTheme);

        return $this;
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
     * Check if there are any themes assigned
     *
     * @return bool
     */
    protected function _hasThemeAssigned()
    {
        /** @var $themeService Mage_Core_Model_Theme_Service */
        $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
        return count($themeService->getAssignedThemeCustomizations()) > 0;
    }

    /**
     * Load layout
     */
    protected function _renderStoreDesigner()
    {
        try {
            $this->_setTitle();
            $this->loadLayout();
            $this->_setActiveMenu('Mage_DesignEditor::system_design_editor');
            if (!$this->_isFirstEntrance()) {
                /** @var $themeService Mage_Core_Model_Theme_Service */
                $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');

                /** @var $assignedThemeBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Assigned */
                $assignedThemeBlock = $this->getLayout()->getBlock('assigned.theme.list');
                $assignedThemeBlock->setCollection($themeService->getAssignedThemeCustomizations());

                /** @var $unassignedThemeBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Unassigned */
                $unassignedThemeBlock = $this->getLayout()->getBlock('unassigned.theme.list');
                $unassignedThemeBlock->setCollection($themeService->getUnassignedThemeCustomizations());
                $unassignedThemeBlock->setHasThemeAssigned($this->_hasThemeAssigned());
            }
            /** @var $storeViewBlock Mage_DesignEditor_Block_Adminhtml_Theme_Selector_StoreView */
            $storeViewBlock = $this->getLayout()->getBlock('theme.selector.storeview');
            $storeViewBlock->setData('actionOnAssign', 'refresh');
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('We can\'t load the list of themes.'));
            $this->_redirectUrl($this->_getRefererUrl());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Resolve which action should be actually performed and forward to it
     *
     * @return bool Is forwarding was done
     */
    protected function _resolveForwarding()
    {
        $action = $this->_isFirstEntrance() ? 'firstEntrance' : 'index';
        if ($action != $this->getRequest()->getActionName()) {
            $this->_forward($action);
            return true;
        };

        return false;
    }

    /**
     * Get current url
     *
     * @param null|string $themeId
     * @param null|string $mode
     * @return string
     */
    protected function _getCurrentUrl($themeId = null, $mode = null)
    {
        /** @var $vdeUrlModel Mage_DesignEditor_Model_Url_NavigationMode */
        $vdeUrlModel = $this->_objectManager->create('Mage_DesignEditor_Model_Url_NavigationMode', array(
             'data' => array('mode' => $mode, 'themeId' => $themeId)
        ));
        $url = $this->_getSession()->getData(Mage_DesignEditor_Model_State::CURRENT_URL_SESSION_KEY);
        if (empty($url)) {
            $url = '';
        }
        return $vdeUrlModel->getUrl(ltrim($url, '/'));
    }
}
