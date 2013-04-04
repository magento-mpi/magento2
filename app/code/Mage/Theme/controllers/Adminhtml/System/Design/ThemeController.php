<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme controller
 */
class Mage_Theme_Adminhtml_System_Design_ThemeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_eventManager->dispatch('theme_registration_from_filesystem');
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Theme::system_design_theme');
        $this->renderLayout();
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Create new theme
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit theme
     */
    public function editAction()
    {
        $themeId = (int)$this->getRequest()->getParam('id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        try {
            $theme->setType(Mage_Core_Model_Theme::TYPE_VIRTUAL);
            if ($themeId && (!$theme->load($themeId)->getId() || !$theme->isVisible())) {
                throw new Mage_Core_Exception($this->__('We cannot find theme "%s".', $themeId));
            }
            /** @var $cssFileModel Mage_Core_Model_Theme_Customization_Files_Css */
            $cssFileModel = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css');
            /** @var $jsFileModel Mage_Core_Model_Theme_Customization_Files_Js */
            $jsFileModel = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
            $theme->setCustomization($cssFileModel)->setCustomization($jsFileModel);

            $jsFileModel->removeTemporaryFiles($theme);
            Mage::register('current_theme', $theme);

            $this->loadLayout();
            /** @var $tab Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css */
            $tab = $this->getLayout()->getBlock('theme_edit_tabs_tab_css_tab');
            if ($tab && $tab->canShowTab()) {
                /** @var $helper Mage_Core_Helper_Theme */
                $helper = $this->_objectManager->get('Mage_Core_Helper_Theme');
                $files = $helper->getGroupedCssFiles($theme);
                $tab->setFiles($files);
            }
            $this->_setActiveMenu('Mage_Theme::system_design_theme');
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('We cannot find the theme.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectBack = (bool)$this->getRequest()->getParam('back', false);
        $themeData = $this->getRequest()->getParam('theme');
        $customCssData = $this->getRequest()->getParam('custom_css_content');
        $uploadJsFiles = (array)$this->getRequest()->getParam('js_uploaded_files');
        $removeJsFiles = (array)$this->getRequest()->getParam('js_removed_files');
        $reorderJsFiles = array_keys($this->getRequest()->getParam('js_order', array()));

        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        /** @var $themeCss Mage_Core_Model_Theme_Customization_Files_Css */
        $themeCss = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css');
        /** @var $themeJs Mage_Core_Model_Theme_Customization_Files_Js */
        $themeJs = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');

        try {
            if ($this->getRequest()->getPost()) {
                $themeCss->setDataForSave(
                    array(Mage_Core_Model_Theme_Customization_Files_Css::CUSTOM_CSS => $customCssData)
                );
                $theme->setCustomization($themeCss);

                $themeJs->setDataForSave($uploadJsFiles);
                $themeJs->setDataForDelete($removeJsFiles);
                $themeJs->setJsOrderData($reorderJsFiles);
                $theme->setCustomization($themeJs);

                if (isset($themeData['theme_id'])) {
                    $theme->load($themeData['theme_id']);
                    if ($theme->getId() && !$theme->isEditable()) {
                        throw new Mage_Core_Exception($this->_helper->__('Theme isn\'t editable.'));
                    }
                }
                $theme->addData($themeData);
                if (isset($themeData['preview']['delete'])) {
                    $theme->getThemeImage()->removePreviewImage();
                }
                $theme->getThemeImage()->uploadPreviewImage('preview');
                $theme->setType(Mage_Core_Model_Theme::TYPE_VIRTUAL);
                $theme->save();
                $this->_getSession()->addSuccess($this->__('You saved the theme.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_getSession()->setThemeData($themeData);
            $this->_getSession()->setThemeCustomCssData($customCssData);
            $redirectBack = true;
        } catch (Exception $e) {
            $this->_getSession()->addError('The theme was not saved');
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $redirectBack ? $this->_redirect('*/*/edit', array('id' => $theme->getId())) : $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $redirectBack = (bool)$this->getRequest()->getParam('back', false);
        $themeId = $this->getRequest()->getParam('id');
        try {
            if ($themeId) {
                /** @var $theme Mage_Core_Model_Theme */
                $theme = $this->_objectManager->create('Mage_Core_Model_Theme')->load($themeId);
                if (!$theme->getId()) {
                    throw new InvalidArgumentException(sprintf('We cannot find a theme with id "%d".', $themeId));
                }
                if (!$theme->isVirtual()) {
                    throw new InvalidArgumentException(
                        sprintf('Only virtual theme is possible to delete and theme "%s" isn\'t virtual', $themeId)
                    );
                }
                $theme->delete();
                $this->_getSession()->addSuccess($this->__('You deleted the theme.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('We cannot delete the theme.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        /**
         * @todo Temporary solution. Theme module should not know about the existence of editor module.
         */
        $redirectBack ? $this->_redirect('*/system_design_editor/index/') : $this->_redirect('*/*/');
    }

    /**
     * Upload css file
     */
    public function uploadCssAction()
    {
        /** @var $serviceModel Mage_Theme_Model_Uploader_Service */
        $serviceModel = $this->_objectManager->get('Mage_Theme_Model_Uploader_Service');
        try {
            $cssFileContent = $serviceModel->uploadCssFile('css_file_uploader')->getFileContent();
            $result = array('error' => false, 'content' => $cssFileContent);
        } catch (Mage_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $this->__('We cannot upload the CSS file.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Upload js file
     *
     * @throws Mage_Core_Exception
     */
    public function uploadJsAction()
    {
        /** @var $serviceModel Mage_Theme_Model_Uploader_Service */
        $serviceModel = $this->_objectManager->get('Mage_Theme_Model_Uploader_Service');
        $themeId = $this->getRequest()->getParam('id');
        try {
            /** @var $theme Mage_Core_Model_Theme */
            $theme = $this->_objectManager->create('Mage_Core_Model_Theme')->load($themeId);
            if (!$theme->getId()) {
                Mage::throwException($this->__('We cannot find a theme with id "%d".', $themeId));
            }
            $serviceModel->uploadJsFile('js_files_uploader', $theme);

            /** @var $filesJs Mage_Core_Model_Theme_Customization_Files_Js */
            $filesJs = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
            /** @var $customJsFiles Mage_Core_Model_Resource_Theme_File_Collection */
            $customJsFiles = $theme->setCustomization($filesJs)
                ->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Js::TYPE);
            $result = array('error' => false, 'files' => $customJsFiles->getFilesInfo());
        } catch (Mage_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $this->__('We cannot upload the JS file.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Download custom css file
     */
    public function downloadCustomCssAction()
    {
        $themeId = $this->getRequest()->getParam('theme_id');
        try {
            /** @var $theme Mage_Core_Model_Theme */
            $theme = $this->_objectManager->create('Mage_Core_Model_Theme')->load($themeId);
            if (!$theme->getId()) {
                throw new InvalidArgumentException(sprintf('We cannot find a theme with id "%d".', $themeId));
            }

            /** @var $filesCss Mage_Core_Model_Theme_Customization_Files_Css */
            $filesCss = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css');
            /** @var $customCssFile Mage_Core_Model_Theme_File */
            $customCssFile = $theme->setCustomization($filesCss)
                ->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Css::TYPE)->getFirstItem();

            if ($customCssFile->getContent()) {
                $this->_prepareDownloadResponse(
                    basename($filesCss->getFilePathByType(Mage_Core_Model_Theme_Customization_Files_Css::CUSTOM_CSS)),
                    array(
                        'type'  => 'filename',
                        'value' => $customCssFile->getFullPath()
                    )
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e,
                $this->__('We cannot find file "%s"', Mage_Core_Model_Theme_Customization_Files_Css::CUSTOM_CSS));
            $this->_redirectUrl($this->_getRefererUrl());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Download css file
     */
    public function downloadCssAction()
    {
        $themeId = $this->getRequest()->getParam('theme_id');
        $file = $this->getRequest()->getParam('file');

        /** @var $helper Mage_Core_Helper_Theme */
        $helper = $this->_objectManager->get('Mage_Core_Helper_Theme');
        $fileName = $helper->urlDecode($file);
        try {
            /** @var $theme Mage_Core_Model_Theme */
            $theme = $this->_objectManager->create('Mage_Core_Model_Theme')->load($themeId);
            if (!$theme->getId()) {
                throw new InvalidArgumentException(sprintf('We cannot find a theme with id "%d".', $themeId));
            }

            $themeCss = $helper->getCssFiles($theme);
            if (!isset($themeCss[$fileName])) {
                throw new InvalidArgumentException(
                    sprintf('Css file "%s" is not in the theme with id "%d".', $fileName, $themeId)
                );
            }

            $this->_prepareDownloadResponse($fileName, array(
                'type'  => 'filename',
                'value' => $themeCss[$fileName]['path']
            ));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('We cannot find file "%s".', $fileName));
            $this->_redirectUrl($this->_getRefererUrl());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Check the permission to manage themes
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Theme::theme');
    }
}
