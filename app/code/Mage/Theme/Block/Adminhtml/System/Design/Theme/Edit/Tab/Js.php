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
 * Theme form, Js editor tab
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Js
    extends Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_TabAbstract
{
    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Js
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $this->_addThemeJsFieldset();
        parent::_prepareForm();
        return $this;
    }

    /**
     * Set theme js fieldset
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Js
     */
    protected function _addThemeJsFieldset()
    {
        $form = $this->getForm();
        $themeFieldset = $form->addFieldset('theme_js', array(
            'legend' => $this->__('Theme Java Script'),
        ));
        $customization = $this->_getCurrentTheme()->getCustomization();
        $customJsFiles = $customization->getFilesByType(Mage_Core_Model_Theme_Customization_File_Js::TYPE);

        /** @var $jsFieldsetRenderer Mage_Backend_Block_Widget_Form_Renderer_Fieldset */
        $jsFieldsetRenderer = $this->getChildBlock('theme_edit_tabs_tab_js_tab_content');
        $jsFieldsetRenderer->setJsFiles($customization->generateFileInfo($customJsFiles));

        $jsFieldset = $themeFieldset->addFieldset('js_fieldset_javascript_content', array('class' => 'fieldset-wide'));

        $this->_addElementTypes($themeFieldset);

        $themeFieldset->addField('js_files_uploader', 'js_files', array(
            'name'     => 'js_files_uploader',
            'label'    => $this->__('Select JS Files to Upload'),
            'title'    => $this->__('Select JS Files to Upload'),
            'accept'   => 'application/x-javascript',
            'multiple' => '',
            'note'     => $this->_getUploadJsFileNote()
        ));

        $themeFieldset->addField('js_uploader_button', 'button', array(
            'name'     => 'js_uploader_button',
            'value'    => $this->__('Upload JS Files'),
            'disabled' => 'disabled',
        ));

        $jsFieldset->setRenderer($jsFieldsetRenderer);
        return $this;
    }

    /**
     * Set additional form field type
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $fileElement = 'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File';
        return array('js_files' => $fileElement);
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('JS Editor');
    }

    /**
     * Get upload js url
     *
     * @return string
     */
    public function getJsUploadUrl()
    {
        return $this->getUrl('*/system_design_theme/uploadjs', array('id' => $this->_getCurrentTheme()->getId()));
    }

    /**
     * Get note string for js file to Upload
     *
     * @return string
     */
    protected function _getUploadJsFileNote()
    {
        return $this->__('Allowed file types *.js.');
    }
}
