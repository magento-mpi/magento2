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
     * @return Mage_Adminhtml_Block_System_Design_Theme_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $formData = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getThemeData(true);
        if (!$formData) {
            $formData = Mage::registry('theme')->getData();
        }

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('general', array(
            'legend'   => $this->__('General Settings'),
        ));

        if (isset($formData['theme_id'])) {
            $fieldset->addField('theme_id', 'hidden', array(
                'name' => 'theme_id'
            ));
        }

        $fieldset->addField('theme_area', 'text', array(
            'label'    => $this->__('Theme Area'),
            'title'    => $this->__('Theme Area'),
            'name'     => 'theme_area',
            'required' => true
        ));

        $fieldset->addField('theme_package', 'text', array(
            'label'    => $this->__('Theme Package'),
            'title'    => $this->__('Theme Package'),
            'name'     => 'theme_package',
            'required' => true
        ));

        $fieldset->addField('theme_code', 'text', array(
            'label'    => $this->__('Theme Code'),
            'title'    => $this->__('Theme Code'),
            'name'     => 'theme_code',
            'required' => true
        ));

        $fieldset->addField('theme_skin', 'text', array(
            'label'    => $this->__('Theme Skin'),
            'title'    => $this->__('Theme Skin'),
            'name'     => 'theme_skin',
            'required' => true
        ));

        $form->addValues($formData);
        $form->setFieldNameSuffix('theme');
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('General');
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
