<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend controller for the design editor
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_DesignEditor_Controller_Adminhtml_System_Design_Editor_Tools extends Magento_Adminhtml_Controller_Action
{
    /**
     * Initialize theme context model
     *
     * @return Magento_DesignEditor_Model_Theme_Context
     */
    protected function _initContext()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        /** @var Magento_DesignEditor_Model_Theme_Context $themeContext */
        $themeContext = $this->_objectManager->get('Magento_DesignEditor_Model_Theme_Context');
        return $themeContext->setEditableThemeById($themeId);
    }

    /**
     *  Upload custom CSS action
     */
    public function uploadAction()
    {
        /** @var $cssService Magento_Theme_Model_Theme_Customization_File_CustomCss */
        $cssService = $this->_objectManager->get('Magento_Theme_Model_Theme_Customization_File_CustomCss');
        /** @var $singleFile Magento_Theme_Model_Theme_SingleFile */
        $singleFile = $this->_objectManager->create('Magento_Theme_Model_Theme_SingleFile',
            array('fileService' => $cssService));
        /** @var $serviceModel Magento_Theme_Model_Uploader_Service */
        $serviceModel = $this->_objectManager->get('Magento_Theme_Model_Uploader_Service');
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $cssFileData = $serviceModel->uploadCssFile(
                Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom::FILE_ELEMENT_NAME
            );
            $singleFile->update($editableTheme, $cssFileData['content']);
            $response = array(
                'success' => true,
                'message' => __('You updated the custom.css file.'),
                'content' => $cssFileData['content']
            );
        } catch (Magento_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => __('We cannot upload the CSS file.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Save custom css file
     */
    public function saveCssContentAction()
    {
        $customCssContent = (string)$this->getRequest()->getParam('custom_css_content', '');
        /** @var $cssService Magento_Theme_Model_Theme_Customization_File_CustomCss */
        $cssService = $this->_objectManager->get('Magento_Theme_Model_Theme_Customization_File_CustomCss');
        /** @var $singleFile Magento_Theme_Model_Theme_SingleFile */
        $singleFile = $this->_objectManager->create('Magento_Theme_Model_Theme_SingleFile',
            array('fileService' => $cssService));
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $customCss = $singleFile->update($editableTheme, $customCssContent);
            $response = array(
                'success'  => true,
                'filename' => $customCss->getFileName(),
                'message'  => __('You updated the %1 file.', $customCss->getFileName())
            );
        } catch (Magento_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => __('We can\'t save the custom css file.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Ajax list of existing javascript files
     */
    public function jsListAction()
    {
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $customization = $editableTheme->getCustomization();
            $customJsFiles = $customization->getFilesByType(Magento_Core_Model_Theme_Customization_File_Js::TYPE);
            $result = array('error' => false, 'files' => $customization->generateFileInfo($customJsFiles));
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Upload js file
     */
    public function uploadJsAction()
    {
        /** @var $serviceModel Magento_Theme_Model_Uploader_Service */
        $serviceModel = $this->_objectManager->get('Magento_Theme_Model_Uploader_Service');
        /** @var $jsService Magento_Core_Model_Theme_Customization_File_Js */
        $jsService = $this->_objectManager->create('Magento_Core_Model_Theme_Customization_File_Js');
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $jsFileData = $serviceModel->uploadJsFile('js_files_uploader');
            $jsFile = $jsService->create();
            $jsFile->setTheme($editableTheme);
            $jsFile->setFileName($jsFileData['filename']);
            $jsFile->setData('content', $jsFileData['content']);
            $jsFile->save();
            $this->_forward('jsList');
            return;
        } catch (Magento_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => __('We cannot upload the JS file.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Delete custom file action
     */
    public function deleteCustomFilesAction()
    {
        $removeJsFiles = (array)$this->getRequest()->getParam('js_removed_files');
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $editableTheme->getCustomization()->delete($removeJsFiles);
            $this->_forward('jsList');
        } catch (Exception $e) {
            $this->_redirectUrl($this->_getRefererUrl());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Reorder js file
     */
    public function reorderJsAction()
    {
        $reorderJsFiles = (array)$this->getRequest()->getParam('js_order', array());
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $editableTheme->getCustomization()->reorder(
                Magento_Core_Model_Theme_Customization_File_Js::TYPE, $reorderJsFiles
            );
            $result = array('success' => true);
        } catch (Magento_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => __('We cannot upload the CSS file.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Save image sizes
     */
    public function saveImageSizingAction()
    {
        $imageSizing = $this->getRequest()->getParam('imagesizing');
        /** @var $configFactory Magento_DesignEditor_Model_Editor_Tools_Controls_Factory */
        $configFactory = $this->_objectManager->create('Magento_DesignEditor_Model_Editor_Tools_Controls_Factory');
        /** @var $imageSizingValidator Magento_DesignEditor_Model_Editor_Tools_ImageSizing_Validator */
        $imageSizingValidator = $this->_objectManager->get(
            'Magento_DesignEditor_Model_Editor_Tools_ImageSizing_Validator'
        );
        try {
            $themeContext = $this->_initContext();
            $configuration = $configFactory->create(
                Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_IMAGE_SIZING,
                $themeContext->getStagingTheme(),
                $themeContext->getEditableTheme()->getParentTheme()
            );
            $imageSizing = $imageSizingValidator->validate($configuration->getAllControlsData(), $imageSizing);
            $configuration->saveData($imageSizing);
            $result = array('success' => true, 'message' => __('We saved the image sizes.'));
        } catch (Magento_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => __('We can\'t save image sizes.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));

    }

    /**
     * Upload quick style image
     */
    public function uploadQuickStyleImageAction()
    {
        /** @var $uploaderModel Magento_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader */
        $uploaderModel = $this->_objectManager
            ->get('Magento_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader');
        try {
            /** @var $configFactory Magento_DesignEditor_Model_Editor_Tools_Controls_Factory */
            $configFactory = $this->_objectManager->create('Magento_DesignEditor_Model_Editor_Tools_Controls_Factory');
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $keys = array_keys($this->getRequest()->getFiles());
            $result = $uploaderModel->setTheme($editableTheme)->uploadFile($keys[0]);

            $configuration = $configFactory->create(
                Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES,
                $editableTheme,
                $themeContext->getEditableTheme()->getParentTheme()
            );
            $configuration->saveData(array($keys[0] => $result['css_path']));

            $response = array('error' => false, 'content' => $result);
        } catch (Magento_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $errorMessage = __('Something went wrong uploading the image.' .
                ' Please check the file format and try again (JPEG, GIF, or PNG).');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Remove quick style image
     */
    public function removeQuickStyleImageAction()
    {
        $fileName = $this->getRequest()->getParam('file_name', false);
        $elementName = $this->getRequest()->getParam('element', false);

        /** @var $uploaderModel Magento_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader */
        $uploaderModel = $this->_objectManager
            ->get('Magento_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader');
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $result = $uploaderModel->setTheme($editableTheme)->removeFile($fileName);

            /** @var $configFactory Magento_DesignEditor_Model_Editor_Tools_Controls_Factory */
            $configFactory = $this->_objectManager->create('Magento_DesignEditor_Model_Editor_Tools_Controls_Factory');

            $configuration = $configFactory->create(
                Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES,
                $editableTheme,
                $themeContext->getEditableTheme()->getParentTheme()
            );
            $configuration->saveData(array($elementName => ''));

            $response = array('error' => false, 'content' => $result);
        } catch (Magento_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $errorMessage = __('Something went wrong uploading the image.' .
                ' Please check the file format and try again (JPEG, GIF, or PNG).');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Upload store logo
     *
     * @throws Magento_Core_Exception
     */
    public function uploadStoreLogoAction()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        try {
            /** @var $theme Magento_Core_Model_Theme */
            $theme = $this->_objectManager->create('Magento_Core_Model_Theme');
            if (!$theme->load($themeId)->getId() || !$theme->isEditable()) {
                throw new Magento_Core_Exception(
                    __('The file can\'t be found or edited.')
                );
            }

            /** @var $customizationConfig Magento_Theme_Model_Config_Customization */
            $customizationConfig = $this->_objectManager->get('Magento_Theme_Model_Config_Customization');
            $store = $this->_objectManager->get('Magento_Core_Model_Store')->load($storeId);

            if (!$customizationConfig->isThemeAssignedToStore($theme, $store)) {
                throw new Magento_Core_Exception(__('This theme is not assigned to a store view #%1.',
                    $theme->getId()));
            }
            /** @var $storeLogo Magento_DesignEditor_Model_Editor_Tools_QuickStyles_LogoUploader */
            $storeLogo = $this->_objectManager->get('Magento_DesignEditor_Model_Editor_Tools_QuickStyles_LogoUploader');
            $storeLogo->setScope('stores')->setScopeId($store->getId())->setPath('design/header/logo_src')->save();

            $this->_reinitSystemConfiguration();

            $response = array('error' => false, 'content' => array('name' => basename($storeLogo->getValue())));
        } catch (Magento_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $errorMessage = __('Something went wrong uploading the image.' .
                ' Please check the file format and try again (JPEG, GIF, or PNG).');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Remove store logo
     *
     * @throws Magento_Core_Exception
     */
    public function removeStoreLogoAction()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        try {
            /** @var $theme Magento_Core_Model_Theme */
            $theme = $this->_objectManager->create('Magento_Core_Model_Theme');
            if (!$theme->load($themeId)->getId() || !$theme->isEditable()) {
                throw new Magento_Core_Exception(
                    __('The file can\'t be found or edited.')
                );
            }

            /** @var $customizationConfig Magento_Theme_Model_Config_Customization */
            $customizationConfig = $this->_objectManager->get('Magento_Theme_Model_Config_Customization');
            $store = $this->_objectManager->get('Magento_Core_Model_Store')->load($storeId);

            if (!$customizationConfig->isThemeAssignedToStore($theme, $store)) {
                throw new Magento_Core_Exception(__('This theme is not assigned to a store view #%1.',
                    $theme->getId()));
            }

            $this->_objectManager->get('Magento_Backend_Model_Config_Backend_Store')
                ->setScope('stores')->setScopeId($store->getId())->setPath('design/header/logo_src')
                ->setValue('')->save();

            $this->_reinitSystemConfiguration();
            $response = array('error' => false, 'content' => array());
        } catch (Magento_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $errorMessage = __('Something went wrong uploading the image.' .
                ' Please check the file format and try again (JPEG, GIF, or PNG).');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Save quick styles data
     */
    public function saveQuickStylesAction()
    {
        $controlId = $this->getRequest()->getParam('id');
        $controlValue = $this->getRequest()->getParam('value');
        try {
            $themeContext = $this->_initContext();
            /** @var $configFactory Magento_DesignEditor_Model_Editor_Tools_Controls_Factory */
            $configFactory = $this->_objectManager->create('Magento_DesignEditor_Model_Editor_Tools_Controls_Factory');
            $configuration = $configFactory->create(
                Magento_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES,
                $themeContext->getStagingTheme(),
                $themeContext->getEditableTheme()->getParentTheme()
            );
            $configuration->saveData(array($controlId => $controlValue));
            $response = array('success' => true);
        } catch (Magento_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        } catch (Exception $e) {
            $errorMessage = __('Something went wrong uploading the image.' .
                ' Please check the file format and try again (JPEG, GIF, or PNG).');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Re-init system configuration
     *
     * @return Magento_Core_Model_Config
     */
    protected function _reinitSystemConfiguration()
    {
        /** @var $configModel Magento_Core_Model_Config */
        $configModel = $this->_objectManager->get('Magento_Core_Model_Config');
        return $configModel->reinit();
    }
}
