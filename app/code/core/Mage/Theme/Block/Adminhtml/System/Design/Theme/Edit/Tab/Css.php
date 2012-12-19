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
 * Theme form, Css editor tab
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
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
        $this->_addThemeCssFieldset();
    }

    /**
     * Set theme css fieldset
     */
    protected function _addThemeCssFieldset()
    {
        $form = $this->getForm();
        $themeFieldset = $form->addFieldset('theme_css', array(
            'legend' => $this->__('Theme CSS'),
        ));
        $this->_addElementTypes($themeFieldset);
        $themeFieldset->addField('theme_css_view', 'links', array(
            'label'  => $this->__('View theme CSS'),
            'title'  => $this->__('View theme CSS'),
            'name'   => 'links',
            'values' => $this->_getThemeCssList()
        ));
    }

    /**
     * Prepare file items for output on page for download
     *
     * @return array
     */
    protected function _getThemeCssList()
    {
        $files = $this->_getThemeCssFiles();
        $data = array();
        foreach ($files as $file) {
            $data[] = array(
                'href'      => $file['title'],
                'label'     => $file['title'],
                'delimiter' => '<br />',
            );
        }
        return $data;
    }

    /**
     * Return array css files for theme
     *
     * @return array
     */
    protected function _getThemeCssFiles()
    {
        return array(
            array('title' => 'main.css'),
            array('title' => 'print.css')
        );
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
     * Set additional form field type for theme preview image
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $element = Mage::getConfig()
            ->getBlockClassName('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Links');
        return array('links' => $element);
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('CSS Editor');
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
        return $this->_getCurrentTheme()->isVirtual();
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
