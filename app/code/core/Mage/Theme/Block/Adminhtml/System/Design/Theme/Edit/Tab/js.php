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
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_General|Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $this->_addThemeJsFieldset();
    }

    /**
     * Set theme js fieldset
     */
    protected function _addThemeJsFieldset()
    {
        $form = $this->getForm();
        $themeFieldset = $form->addFieldset('theme_js', array(
        'legend' => $this->__('Theme JS'),
        ));
        $this->_addElementTypes($themeFieldset);
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
}
