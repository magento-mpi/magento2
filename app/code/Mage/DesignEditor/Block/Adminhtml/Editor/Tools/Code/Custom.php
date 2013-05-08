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
 * Block that renders Custom tab
 *
 * @method Mage_Core_Model_Theme getTheme()
 * @method setTheme($theme)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom extends Mage_Backend_Block_Widget_Form
{
    /**
     * Upload file element html id
     */
    const FILE_ELEMENT_NAME = 'css_file_uploader';

    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'action'   => '#',
            'method'   => 'post'
        ));
        $this->setForm($form);
        $form->setUseContainer(true);

        $form->addType('css_file', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader');

        $form->addField($this->getFileElementName(), 'css_file', array(
            'name'     => $this->getFileElementName(),
            'accept'   => 'text/css',
            'no_span'  => true
        ));

        parent::_prepareForm();
        return $this;
    }

    /**
     * Get url to download custom CSS file
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string
     */
    public function getDownloadCustomCssUrl($theme)
    {
        return $this->getUrl('*/system_design_theme/downloadCustomCss', array('theme_id' => $theme->getThemeId()));
    }

    /**
     * Get url to save custom CSS file
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string
     */
    public function getSaveCustomCssUrl($theme)
    {
        return $this->getUrl('*/system_design_editor_tools/saveCssContent', array('theme_id' => $theme->getThemeId()));
    }

    /**
     * Get theme custom css content
     *
     * @param string $targetElementId
     * @param Mage_Core_Model_Theme $theme
     * @param string $contentType
     * @return string
     */
    public function getMediaBrowserUrl($targetElementId, $theme, $contentType)
    {
        return $this->getUrl('*/system_design_editor_files/index', array(
            'target_element_id'                           => $targetElementId,
            Mage_Theme_Helper_Storage::PARAM_THEME_ID     => $theme->getThemeId(),
            Mage_Theme_Helper_Storage::PARAM_CONTENT_TYPE => $contentType
        ));
    }

    /**
     * Get theme custom css content
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string
     */
    public function getCustomCssContent($theme)
    {
        /** @var $cssFile Mage_Core_Model_Theme_File */
        $cssFile = $theme->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Css::TYPE)->getFirstItem();
        return $cssFile->getContent();
    }

    /**
     * Get custom CSS file name
     *
     * @return string
     */
    public function getCustomFileName()
    {
        return pathinfo(Mage_Core_Model_Theme_Customization_Files_Css::CUSTOM_CSS, PATHINFO_BASENAME);
    }

    /**
     * Get file element name
     *
     * @return string
     */
    public function getFileElementName()
    {
        return self::FILE_ELEMENT_NAME;
    }
}
