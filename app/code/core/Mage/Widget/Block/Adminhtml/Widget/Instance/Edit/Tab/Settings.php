<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance Settings tab block
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Settings
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Widget_Helper_Data')->__('Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Mage_Widget_Helper_Data')->__('Settings');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return !(bool)$this->getWidgetInstance()->isCompleteToCreate();
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
     * Getter
     *
     * @return Mage_Widget_Model_Widget_Instance
     */
    public function getWidgetInstance()
    {
        return Mage::registry('current_widget_instance');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Settings
     */
    protected function _prepareForm()
    {
        $widgetInstance = $this->getWidgetInstance();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('Mage_Widget_Helper_Data')->__('Settings'))
        );

        $this->_addElementTypes($fieldset);

        $fieldset->addField('instance_type', 'select', array(
            'name'  => 'instance_type',
            'label' => Mage::helper('Mage_Widget_Helper_Data')->__('Type'),
            'title' => Mage::helper('Mage_Widget_Helper_Data')->__('Type'),
            'class' => '',
            'values' => $this->getTypesOptionsArray()
        ));

        $fieldset->addField('package_theme', 'select', array(
            'name'  => 'package_theme',
            'label' => Mage::helper('Mage_Widget_Helper_Data')->__('Design Package/Theme'),
            'title' => Mage::helper('Mage_Widget_Helper_Data')->__('Design Package/Theme'),
            'required' => false,
            'values'   => $this->getPackageThemeOptionsArray()
        ));
        $continueButton = $this->getLayout()
            ->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'     => Mage::helper('Mage_Widget_Helper_Data')->__('Continue'),
                'onclick'   => "setSettings('" . $this->getContinueUrl() . "', 'instance_type', 'package_theme')",
                'class'     => 'save'
            ));
        $fieldset->addField('continue_button', 'note', array(
            'text' => $continueButton->toHtml(),
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return url for continue button
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/*', array(
            '_current' => true,
            'instance_type' => '{{instance_type}}',
            'package_theme' => '{{package_theme}}'
        ));
    }

    /**
     * Retrieve array (widget_type => widget_name) of available widgets
     *
     * @return array
     */
    public function getTypesOptionsArray()
    {
        $widgets = $this->getWidgetInstance()->getWidgetsOptionArray();
        array_unshift($widgets, array(
            'value' => '',
            'label' => Mage::helper('Mage_Widget_Helper_Data')->__('-- Please Select --')
        ));
        return $widgets;
    }

    /**
     * User-defined widgets sorting by Name
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortWidgets($a, $b)
    {
        return strcmp($a["label"], $b["label"]);
    }

    /**
     * Retrieve package/theme options array
     *
     * @return array
     */
    public function getPackageThemeOptionsArray()
    {
        $options = Mage::getModel('Mage_Core_Model_Design_Source_Design')->getThemeOptions();
        array_unshift($options, array('value' => '', 'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('-- Please Select --')));
        return $options;
    }
}
