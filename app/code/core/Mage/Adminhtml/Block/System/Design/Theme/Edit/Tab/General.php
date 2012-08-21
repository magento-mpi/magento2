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

        $packageFieldset = $form->addFieldset('package', array(
            'legend'   => $this->__('Package Settings'),
        ));

        $themeFieldset = $form->addFieldset('theme', array(
            'legend'   => $this->__('Theme Settings'),
        ));

        $requirementsFieldset = $form->addFieldset('requirements', array(
            'legend'   => $this->__('Magento Requirements'),
        ));

        if (isset($formData['theme_id'])) {
            $themeFieldset->addField('theme_id', 'hidden', array(
                'name' => 'theme_id'
            ));
        }

        $packageFieldset->addField('package_code', 'text', array(
            'label'    => $this->__('Package Code'),
            'title'    => $this->__('Package Code'),
            'name'     => 'package_code',
            'class'     => 'validate-code',
            'required' => true
        ));

        $packageFieldset->addField('package_title', 'text', array(
            'label'    => $this->__('Package Title'),
            'title'    => $this->__('Package Title'),
            'name'     => 'package_title',
            'class'     => 'validate-alphanum-with-spaces',
            'required' => true
        ));

        $themeFieldset->addField('parent_theme', 'text', array(
            'label'    => $this->__('Parent theme'),
            'title'    => $this->__('Parent theme'),
            'name'     => 'parent_theme',
            'required' => false
        ));

        $themeFieldset->addField('theme_code', 'text', array(
            'label'    => $this->__('Theme Code'),
            'title'    => $this->__('Theme Code'),
            'name'     => 'theme_code',
            'required' => true
        ));

        $themeFieldset->addField('theme_version', 'text', array(
            'label'    => $this->__('Theme Version'),
            'title'    => $this->__('Theme Version'),
            'name'     => 'theme_version',
            'required' => true
        ));

        $themeFieldset->addField('theme_title', 'text', array(
            'label'    => $this->__('Theme Title'),
            'title'    => $this->__('Theme Title'),
            'name'     => 'theme_title',
            'required' => true
        ));

        $requirementsFieldset->addField('magento_version_from', 'text', array(
            'label'    => $this->__('Magento Version From'),
            'title'    => $this->__('Magento Version From'),
            'name'     => 'magento_version_from',
            'required' => true
        ));

        $requirementsFieldset->addField('magento_version_to', 'text', array(
            'label'    => $this->__('Magento Version To'),
            'title'    => $this->__('Magento Version To'),
            'name'     => 'magento_version_to',
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
