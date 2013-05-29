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
class Mage_DesignEditor_Adminhtml_System_Design_Editor_ToolsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Mage_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Mage_DesignEditor_Model_Theme_Context $themeContext
     * @param string $areaCode
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Mage_DesignEditor_Model_Theme_Context $themeContext,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);
        $this->_themeContext = $themeContext;
    }

    /**
     *  Upload custom CSS action
     */
    public function uploadAction()
    {
        /** @var $themeCss Mage_Core_Model_Theme_Customization_Files_Css */
        $themeCss = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css');
        /** @var $serviceModel Mage_Theme_Model_Uploader_Service */
        $serviceModel = $this->_objectManager->get('Mage_Theme_Model_Uploader_Service');
        try {
            $editableTheme = $this->_themeContext->getStagingTheme();
            $cssFileContent = $serviceModel->uploadCssFile(
                Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom::FILE_ELEMENT_NAME
            )->getFileContent();
            $themeCss->setDataForSave(
                array(Mage_Core_Model_Theme_Customization_Files_Css::CUSTOM_CSS => $cssFileContent)
            );
            $themeCss->saveData($editableTheme);
            $response = array(
                'success' => true,
                'message' => $this->__('Custom.css file has been successfully saved.'),
                'content' => $cssFileContent
            );
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => $this->__('We cannot upload the CSS file.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Save custom css file
     */
    public function saveCssContentAction()
    {
        $customCssContent = (string)$this->getRequest()->getParam('custom_css_content', '');
        try {
            $editableTheme = $this->_themeContext->getStagingTheme();
            /** @var $themeCss Mage_Core_Model_Theme_Customization_Files_Css */
            $themeCss = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css');
            $themeCss->setDataForSave(
                array(Mage_Core_Model_Theme_Customization_Files_Css::CUSTOM_CSS => $customCssContent)
            );
            $editableTheme->setCustomization($themeCss)->save();
            $response = array(
                'success' => true,
                'message' => $this->__('Custom.css file has been successfully saved.')
            );
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => $this->__('Cannot save custom css'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Ajax list of existing javascript files
     */
    public function jsListAction()
    {
        try {
            $editableTheme = $this->_themeContext->getStagingTheme();

            /** @var $filesJs Mage_Core_Model_Theme_Customization_Files_Js */
            $filesJs = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
            /** @var $customJsFiles Mage_Core_Model_Resource_Theme_File_Collection */
            $customJsFiles = $editableTheme->setCustomization($filesJs)
                ->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Js::TYPE);
            $result = array('error' => false, 'files' => $customJsFiles->getFilesInfo());
            $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Upload js file
     */
    public function uploadJsAction()
    {
        /** @var $serviceModel Mage_Theme_Model_Uploader_Service */
        $serviceModel = $this->_objectManager->get('Mage_Theme_Model_Uploader_Service');
        try {
            $editableTheme = $this->_themeContext->getStagingTheme();
            $serviceModel->uploadJsFile('js_files_uploader', $editableTheme, false);
            $editableTheme->setCustomization($serviceModel->getJsFiles())->save();
            $this->_forward('jsList');
            return;
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => $this->__('We cannot upload the JS file.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Delete custom file action
     */
    public function deleteCustomFilesAction()
    {
        $removeJsFiles = (array)$this->getRequest()->getParam('js_removed_files');
        try {
            $editableTheme = $this->_themeContext->getStagingTheme();

            /** @var $themeJs Mage_Core_Model_Theme_Customization_Files_Js */
            $themeJs = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
            $editableTheme->setCustomization($themeJs);

            $themeJs->setDataForDelete($removeJsFiles);
            $editableTheme->save();

            $this->_forward('jsList');
        } catch (Exception $e) {
            $this->_redirectUrl($this->_getRefererUrl());
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
    }

    /**
     * Reorder js file
     */
    public function reorderJsAction()
    {
        $reorderJsFiles = (array)$this->getRequest()->getParam('js_order', array());
        /** @var $themeJs Mage_Core_Model_Theme_Customization_Files_Js */
        $themeJs = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
        try {
            $editableTheme = $this->_themeContext->getStagingTheme();
            $themeJs->setJsOrderData($reorderJsFiles);
            $editableTheme->setCustomization($themeJs);
            $editableTheme->save();

            $result = array('success' => true);
        } catch (Mage_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $this->__('We cannot upload the CSS file.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Save image sizes
     */
    public function saveImageSizingAction()
    {
        $imageSizing = $this->getRequest()->getParam('imagesizing');
        /** @var $configFactory Mage_DesignEditor_Model_Editor_Tools_Controls_Factory */
        $configFactory = $this->_objectManager->create('Mage_DesignEditor_Model_Editor_Tools_Controls_Factory');
        /** @var $imageSizingValidator Mage_DesignEditor_Model_Editor_Tools_ImageSizing_Validator */
        $imageSizingValidator = $this->_objectManager->get(
            'Mage_DesignEditor_Model_Editor_Tools_ImageSizing_Validator'
        );
        try {
            $configuration = $configFactory->create(
                Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_IMAGE_SIZING,
                $this->_themeContext->getStagingTheme(),
                $this->_themeContext->getEditableTheme()->getParentTheme()
            );
            $imageSizing = $imageSizingValidator->validate($configuration->getAllControlsData(), $imageSizing);
            $configuration->saveData($imageSizing);
            $result = array('success' => true, 'message' => $this->__('Image sizes are saved.'));
        } catch (Mage_Core_Exception $e) {
             $result = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $this->__('Cannot save image sizes.'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));

    }

    /**
     * Upload quick style image
     */
    public function uploadQuickStyleImageAction()
    {
        /** @var $uploaderModel Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader */
        $uploaderModel = $this->_objectManager->get('Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader');
        /** @var $configFactory Mage_DesignEditor_Model_Editor_Tools_Controls_Factory */
        $configFactory = $this->_objectManager->create('Mage_DesignEditor_Model_Editor_Tools_Controls_Factory');
        try {
            $editableTheme = $this->_themeContext->getStagingTheme();
            $keys = array_keys($this->getRequest()->getFiles());
            $result = $uploaderModel->setTheme($editableTheme)->uploadFile($keys[0]);

            $configuration = $configFactory->create(
                Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES,
                $editableTheme,
                $this->_themeContext->getEditableTheme()->getParentTheme()
            );
            $configuration->saveData(array($keys[0] => $result['css_path']));

            $response = array('error' => false, 'content' => $result);
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('We cannot upload the image file.');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Remove quick style image
     */
    public function removeQuickStyleImageAction()
    {
        $fileName = $this->getRequest()->getParam('file_name', false);
        $elementName = $this->getRequest()->getParam('element', false);

        /** @var $uploaderModel Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader */
        $uploaderModel = $this->_objectManager->get('Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader');
        try {
            $editableTheme = $this->_themeContext->getStagingTheme();
            $result = $uploaderModel->setTheme($editableTheme)->removeFile($fileName);

            /** @var $configFactory Mage_DesignEditor_Model_Editor_Tools_Controls_Factory */
            $configFactory = $this->_objectManager->create('Mage_DesignEditor_Model_Editor_Tools_Controls_Factory');

            $configuration = $configFactory->create(
                Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES,
                $editableTheme,
                $this->_themeContext->getEditableTheme()->getParentTheme()
            );
            $configuration->saveData(array($elementName => ''));

            $response = array('error' => false, 'content' => $result);
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('We cannot upload the image file.');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Upload store logo
     *
     * @throws Mage_Core_Exception
     */
    public function uploadStoreLogoAction()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        try {
            $theme->load($themeId);
            if (!$theme->getId() || !$theme->isEditable()) {
                throw new Mage_Core_Exception(
                    $this->__('Theme "%s" was not found or cannot be editable.', $theme->getId())
                );
            }

            /** @var $themeService Mage_Core_Model_Theme_Service */
            $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
            $store = $this->_objectManager->get('Mage_Core_Model_Store')->load($storeId);

            if (!$themeService->isThemeAssignedToSpecificStore($theme, $store)) {
                throw new Mage_Core_Exception($this->__('Theme is not assigned to any store.', $theme->getId()));
            }
            /** @var $storeLogo Mage_DesignEditor_Model_Editor_Tools_QuickStyles_LogoUploader */
            $storeLogo = $this->_objectManager->get('Mage_DesignEditor_Model_Editor_Tools_QuickStyles_LogoUploader');
            $storeLogo->setScope('stores')->setScopeId($store->getId())->setPath('design/header/logo_src')->save();

            $this->_reinitSystemConfiguration();

            $response = array('error' => false, 'content' => array('name' => basename($storeLogo->getValue())));
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('We cannot upload the image file.');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Remove store logo
     *
     * @throws Mage_Core_Exception
     */
    public function removeStoreLogoAction()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        try {
            $theme->load($themeId);
            if (!$theme->getId() || !$theme->isEditable()) {
                throw new Mage_Core_Exception(
                    $this->__('Theme "%s" was not found or cannot be editable.', $theme->getId())
                );
            }

            /** @var $themeService Mage_Core_Model_Theme_Service */
            $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
            $store = $this->_objectManager->get('Mage_Core_Model_Store')->load($storeId);

            if (!$themeService->isThemeAssignedToSpecificStore($theme, $store)) {
                throw new Mage_Core_Exception($this->__('Theme is not assigned to any store.', $theme->getId()));
            }

            $this->_objectManager->get('Mage_Backend_Model_Config_Backend_Store')
                ->setScope('stores')->setScopeId($store->getId())->setPath('design/header/logo_src')
                ->setValue('')->save();

            $this->_reinitSystemConfiguration();

            $response = array('error' => false, 'content' => array());
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('We cannot upload the image file.');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Save quick styles data
     */
    public function saveQuickStylesAction()
    {
        $controlId = $this->getRequest()->getParam('id');
        $controlValue = $this->getRequest()->getParam('value');
        try {
            /** @var $configFactory Mage_DesignEditor_Model_Editor_Tools_Controls_Factory */
            $configFactory = $this->_objectManager->create('Mage_DesignEditor_Model_Editor_Tools_Controls_Factory');
            $configuration = $configFactory->create(
                Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES,
                $this->_themeContext->getStagingTheme(),
                $this->_themeContext->getEditableTheme()->getParentTheme()
            );
            $configuration->saveData(array($controlId => $controlValue));
            $response = array('success' => true);
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Error while saving quick style "%s"', 'some_style_id');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Get theme launched in editor
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _getEditableTheme()
    {
        /** @var $dataHelper Mage_DesignEditor_Helper_Data */
        $dataHelper = $this->_objectManager->get('Mage_DesignEditor_Helper_Data');
        /** @var $helper Mage_Core_Helper_Theme */
        $helper = $this->_objectManager->get('Mage_Core_Helper_Theme');
        return $helper->loadEditableTheme($dataHelper->getEditableThemeId());
    }

    /**
     * Reinit system configuration
     *
     * @return Mage_Core_Model_Config
     */
    protected function _reinitSystemConfiguration()
    {
        /** @var $configModel Mage_Core_Model_Config */
        $configModel = $this->_objectManager->get('Mage_Core_Model_Config');
        return $configModel->reinit();
    }
}
