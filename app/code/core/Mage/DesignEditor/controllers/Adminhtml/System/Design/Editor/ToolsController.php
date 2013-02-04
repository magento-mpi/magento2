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
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => $this->__('Cannot upload css file'));
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($response));
    }

    /**
     * Save custom css file
     */
    public function saveCssContentAction()
    {
        $themeId = $this->getRequest()->getParam('theme_id', false);
        $customCssContent = $this->getRequest()->getParam('custom_css_content', false);
        try {
            if (!$themeId || !$customCssContent) {
                throw new InvalidArgumentException('Param "stores" is not valid');
            }

            $theme = $this->_loadTheme($themeId);

            /** @var $themeCss Mage_Core_Model_Theme_Customization_Files_Css */
            $themeCss = $this->_objectManager->create('Mage_Core_Model_Theme_Customization_Files_Css');
            $themeCss->setDataForSave($customCssContent);
            $theme->setCustomization($themeCss)->save();
            $response = array('error' => false);
        } catch (Mage_Core_Exception $e) {
            $response = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $response = array('error' => true, 'message' => $this->__('Cannot upload css file'));
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
    public function _loadTheme($themeId)
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_objectManager->get('Mage_Core_Model_Theme');
        if (!$themeId || !$theme->load($themeId)->getId()) {
            throw new Mage_Core_Exception($this->__('Theme "%s" was not found.', $themeId));
        }
        if (!$theme->isVirtual()) {
            throw new Mage_Core_Exception($this->__('Theme "%s" is not editable.', $themeId));
        }

        return $theme;
    }
}
