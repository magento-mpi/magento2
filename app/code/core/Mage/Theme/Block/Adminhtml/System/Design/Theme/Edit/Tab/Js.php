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
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Js
    extends Mage_Backend_Block_Widget_Form
    implements Mage_Backend_Block_Widget_Tab_Interface
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

        $jsFieldsetRenderer = $this->getChildBlock('theme_edit_tabs_tab_js_tab_content');
        $jsFieldsetRenderer->setJsFiles($this->_getCurrentTheme()->getCustomJsFiles());
        $this->_getCurrentTheme()->getCustomJsFiles();

        $jsFieldset = $themeFieldset->addFieldset('js_fieldset_javascript_content', array('class' => 'fieldset-wide'));

        $this->_addElementTypes($themeFieldset);

        $themeFieldset->addField('js_files_uploader', 'js_files', array(
            'name'     => 'js_files_uploader',
            'label'    => $this->__('Select JS Files to Upload'),
            'title'    => $this->__('Select JS Files to Upload'),
            'accept'   => 'text/javascript',
            'multiple' => ''
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
        $fileElement = Mage::getConfig()
            ->getBlockClassName('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File');
        return array('js_files' => $fileElement);
    }

    /**
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _getCurrentTheme()
    {
        return Mage::registry('current_theme');
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
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->_getCurrentTheme()->isVirtual() && $this->_getCurrentTheme()->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
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
}
