<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare form tab configuration
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Initialise form fields
     *
     * @return Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Mage_Cms::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $model = Mage::registry('cms_page');

        $layoutFieldset = $form->addFieldset('layout_fieldset', array(
            'legend' => __('Page Layout'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));

        $layoutFieldset->addField('root_template', 'select', array(
            'name'     => 'root_template',
            'label'    => __('Layout'),
            'required' => true,
            'values'   => Mage::getSingleton('Mage_Page_Model_Source_Layout')->toOptionArray(),
            'disabled' => $isElementDisabled
        ));
        if (!$model->getId()) {
            $model->setRootTemplate(Mage::getSingleton('Mage_Page_Model_Source_Layout')->getDefaultValue());
        }

        $layoutFieldset->addField('layout_update_xml', 'textarea', array(
            'name'      => 'layout_update_xml',
            'label'     => __('Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled
        ));

        $designFieldset = $form->addFieldset('design_fieldset', array(
            'legend' => __('Custom Design'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);

        $designFieldset->addField('custom_theme_from', 'date', array(
            'name'      => 'custom_theme_from',
            'label'     => __('Custom Design From'),
            'image'     => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            'disabled'  => $isElementDisabled,
            'class'     => 'validate-date validate-date-range date-range-custom_theme-from'
        ));

        $designFieldset->addField('custom_theme_to', 'date', array(
            'name'      => 'custom_theme_to',
            'label'     => __('Custom Design To'),
            'image'     => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            'disabled'  => $isElementDisabled,
            'class'     => 'validate-date validate-date-range date-range-custom_theme-to'
        ));

        /** @var $label Mage_Core_Model_Theme_Label */
        $label = Mage::getModel('Mage_Core_Model_Theme_Label');
        $options = $label->getLabelsCollection(__('-- Please Select --'));
        $designFieldset->addField('custom_theme', 'select', array(
            'name'      => 'custom_theme',
            'label'     => __('Custom Theme'),
            'values'    => $options,
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_root_template', 'select', array(
            'name'      => 'custom_root_template',
            'label'     => __('Custom Layout'),
            'values'    => Mage::getSingleton('Mage_Page_Model_Source_Layout')->toOptionArray(true),
            'disabled'  => $isElementDisabled
        ));

        $designFieldset->addField('custom_layout_update_xml', 'textarea', array(
            'name'      => 'custom_layout_update_xml',
            'label'     => __('Custom Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled
        ));

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_design_prepare_form', array('form' => $form));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Design');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
