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
class Mage_DesignEditor_Adminhtml_System_Design_Editor_ToolsController extends Mage_Adminhtml_Controller_Action
{
    /**
     *  Upload custom CSS action
     */
    public function uploadAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme');

        /** @var $themeCss Mage_Core_Model_Theme_Customization_Files_Css */
        $themeCss = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css');

        /** @var $serviceModel Mage_Theme_Model_Uploader_Service */
        $serviceModel = $this->_objectManager->get('Mage_Theme_Model_Uploader_Service');
        try {
            $theme = $this->_loadTheme($themeId);

            $cssFileContent = $serviceModel->uploadCssFile(
                Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom::FILE_ELEMENT_NAME
            )->getFileContent();
            $themeCss->setDataForSave($cssFileContent);
            $themeCss->saveData($theme);
            $response = array('error' => false, 'content' => $cssFileContent);
            $this->_session->addSuccess($this->__('Success: Theme custom css was saved.'));
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Cannot upload css file');
            $this->_session->addError($errorMessage);
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->loadLayout();
        $response['message_html'] = $this->getLayout()->getMessagesBlock()->toHtml();
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Save custom css file
     */
    public function saveCssContentAction()
    {
        $themeId = $this->getRequest()->getParam('theme_id', false);
        $customCssContent = $this->getRequest()->getParam('custom_css_content', null);
        try {
            if (!$themeId || (null === $customCssContent)) {
                throw new InvalidArgumentException('Param "stores" is not valid');
            }

            $theme = $this->_loadTheme($themeId);

            /** @var $themeCss Mage_Core_Model_Theme_Customization_Files_Css */
            $themeCss = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css');
            $themeCss->setDataForSave(
                array(Mage_Core_Model_Theme_Customization_Files_Css::CUSTOM_CSS => $customCssContent)
            );
            $theme->setCustomization($themeCss)->save();
            $response = array('error' => false);
            $this->_session->addSuccess($this->__('Theme custom css was saved.'));
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Cannot save custom css');
            $this->_session->addError($errorMessage);
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->loadLayout();
        $response['message_html'] = $this->getLayout()->getMessagesBlock()->toHtml();
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Ajax list of existing javascript files
     */
    public function jsListAction()
    {
        $themeId = $this->getRequest()->getParam('id');
        try {
            $theme = $this->_loadTheme($themeId);
            $this->loadLayout();

            /** @var $filesJs Mage_Core_Model_Theme_Customization_Files_Js */
            $filesJs = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
            $customJsFiles = $theme->setCustomization($filesJs)
                ->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Js::TYPE);

            $jsItemsBlock = $this->getLayout()->getBlock('design_editor_tools_code_js_items');
            $jsItemsBlock->setJsFiles($customJsFiles);

            $result = array('error' => false, 'content' => $jsItemsBlock->toHtml());
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
        $themeId = $this->getRequest()->getParam('id');
        try {
            $theme = $this->_loadTheme($themeId);
            $serviceModel->uploadJsFile('js_files_uploader', $theme, false);
            $theme->setCustomization($serviceModel->getJsFiles())->save();
            $this->_forward('jsList');
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Cannot upload js file');
            $this->_getSession()->addError($errorMessage);
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->loadLayout();
        $response['message_html'] = $this->getLayout()->getMessagesBlock()->toHtml();
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Delete custom file action
     */
    public function deleteCustomFilesAction()
    {
        $themeId = $this->getRequest()->getParam('id');
        $removeJsFiles = (array)$this->getRequest()->getParam('js_removed_files');
        try {
            $theme = $this->_loadTheme($themeId);

            /** @var $themeJs Mage_Core_Model_Theme_Customization_Files_Js */
            $themeJs = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
            $theme->setCustomization($themeJs);

            $themeJs->setDataForDelete($removeJsFiles);
            $theme->save();

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
        $themeId = $this->getRequest()->getParam('id');
        $reorderJsFiles = (array)$this->getRequest()->getParam('js_order', array());
        /** @var $themeJs Mage_Core_Model_Theme_Customization_Files_Js */
        $themeJs = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Js');
        try {
            $theme = $this->_loadTheme($themeId);
            $themeJs->setJsOrderData($reorderJsFiles);
            $theme->setCustomization($themeJs);
            $theme->save();

            $result = array('success' => true);
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $result = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Cannot upload css file');
            $this->_session->addError($errorMessage);
            $result = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->loadLayout();
        $result['message_html'] = $this->getLayout()->getMessagesBlock()->toHtml();
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));
    }

    public function saveImageSizingAction()
    {
        $themeId = $this->getRequest()->getParam('id');
        $imageSizing = $this->getRequest()->getParam('imagesizing');
        /** @var $configFactory Mage_DesignEditor_Model_Editor_Tools_Controls_Factory */
        $configFactory = $this->_objectManager->create('Mage_DesignEditor_Model_Editor_Tools_Controls_Factory');
        try {
            $configuration = $configFactory->create(
                Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_IMAGE_SIZING, $this->_loadTheme($themeId)
            );
            $configuration->saveData($imageSizing);
            $this->_session->addSuccess('Image sizes are saved.');
            $result = array('success' => true);
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $result = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Cannot save image sises.');
            $this->_session->addError($errorMessage);
            $result = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->loadLayout();
        $result['message_html'] = $this->getLayout()->getMessagesBlock()->toHtml();
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));

    }

    /**
     * Upload quick style image
     */
    public function uploadQuickStyleImageAction()
    {
        $themeId = (int)$this->getRequest()->getParam('theme_id');

        /** @var $uploaderModel Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader */
        $uploaderModel = $this->_objectManager->get('Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader');
        /** @var $configFactory Mage_DesignEditor_Model_Editor_Tools_Controls_Factory */
        $configFactory = $this->_objectManager->create('Mage_DesignEditor_Model_Editor_Tools_Controls_Factory');
        try {
            $keys = array_keys($this->getRequest()->getFiles());
            $result = $uploaderModel->setTheme($this->_loadTheme($themeId))->uploadFile($keys[0]);

            $configuration = $configFactory->create(
                Mage_DesignEditor_Model_Editor_Tools_Controls_Factory::TYPE_QUICK_STYLES, $this->_loadTheme($themeId)
            );
            $configuration->saveData(array($keys[0] => $result['css_path']));

            $response = array('error' => false, 'content' => $result);
            $this->_session->addSuccess($this->__('Success: Image file was uploaded.'));
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Cannot upload image file');
            $this->_session->addError($errorMessage);
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

        $themeId = (int)$this->getRequest()->getParam('theme_id');
        $fileName = $this->getRequest()->getParam('file_name', false);

        /** @var $uploaderModel Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader */
        $uploaderModel = $this->_objectManager->get('Mage_DesignEditor_Model_Editor_Tools_QuickStyles_ImageUploader');
        try {
            $result = $uploaderModel->setTheme($this->_loadTheme($themeId))->removeFile($fileName);
            $response = array('error' => false, 'content' => $result);
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Cannot upload image file');
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
        $themeId = (int)$this->getRequest()->getParam('theme_id');
        try {
            $theme = $this->_loadTheme($themeId);

            /** @var $themeService Mage_Core_Model_Theme_Service */
            $themeService = $this->_objectManager->get('Mage_Core_Model_Theme_Service');
            $stores = $themeService->getStoresByThemes();

            if (!isset($stores[$theme->getId()])) {
                throw new Mage_Core_Exception($this->__('Theme is not assigned to any store.', $themeId));
            }

            foreach ($stores[$theme->getId()] as $store) {
                $storeLogo = $this->_objectManager->get('Mage_Backend_Model_Config_Backend_Image_Logo');
                $storeLogo->setScope('stores')->setScopeId($store->getId())->setPath('design/header/logo_src')->save();
            }
            $response = array('error' => false, 'content' => array());
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $errorMessage = $this->__('Cannot upload image file');
            $response = array('error' => true, 'message' => $errorMessage);
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Load theme by theme id
     *
     * Method also checks if theme actually loaded and if theme is virtual or not
     *
     * @param int $themeId
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    protected function _loadTheme($themeId)
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->create('Mage_Core_Model_Theme');
        if (!$themeId || !$theme->load($themeId)->getId()) {
            throw new Mage_Core_Exception($this->__('Theme "%s" was not found.', $themeId));
        }
        if (!$theme->isEditable()) {
            throw new Mage_Core_Exception($this->__('Theme "%s" is not editable.', $themeId));
        }
        return $theme;
    }
}
