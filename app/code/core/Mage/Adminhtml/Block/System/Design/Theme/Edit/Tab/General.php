<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme form, general tab
 */
class Mage_Adminhtml_Block_System_Design_Theme_Edit_Tab_General
    extends Mage_Backend_Block_Widget_Form
    implements Mage_Backend_Block_Widget_Tab_Interface
{

    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Backend_Block_Widget_Form|void
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('Mage_Core_Helper_Data')->__('General Settings')));

        $fieldset->addField('theme_area', 'text', array(
            'label'    => Mage::helper('Mage_Core_Helper_Data')->__('Theme Area'),
            'title'    => Mage::helper('Mage_Core_Helper_Data')->__('Theme Area'),
            'name'     => 'theme_area',
            'required' => true
        ));

        $fieldset->addField('theme_package', 'text', array(
            'label'    => Mage::helper('Mage_Core_Helper_Data')->__('Theme Package'),
            'title'    => Mage::helper('Mage_Core_Helper_Data')->__('Theme Package'),
            'name'     => 'theme_package',
            'required' => true
        ));

        $fieldset->addField('theme_code', 'text', array(
            'label'    => Mage::helper('Mage_Core_Helper_Data')->__('Theme Code'),
            'title'    => Mage::helper('Mage_Core_Helper_Data')->__('Theme Code'),
            'name'     => 'theme_code',
            'required' => true
        ));

        $fieldset->addField('theme_skin', 'text', array(
            'label'    => Mage::helper('Mage_Core_Helper_Data')->__('Theme Skin'),
            'title'    => Mage::helper('Mage_Core_Helper_Data')->__('Theme Skin'),
            'name'     => 'theme_skin',
            'required' => true
        ));

        $formData = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getThemeData(true);
        if (!$formData){
            $formData = Mage::registry('theme')->getData();
        } else {
            $formData = $formData['theme'];
        }

        $form->addValues($formData);
        $form->setFieldNameSuffix('theme');
        $this->setForm($form);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Core_Helper_Data')->__('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Mage_Core_Helper_Data')->__('General');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
