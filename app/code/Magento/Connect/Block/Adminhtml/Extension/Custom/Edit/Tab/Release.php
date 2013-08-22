<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for release info
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Release
    extends Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
{
    /**
     * Prepare Release Info Form before rendering HTML
     *
     * @return Magento_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Release
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = new Magento_Data_Form();
        $form->setHtmlIdPrefix('_release');

        $fieldset = $form->addFieldset('release_fieldset', array(
            'legend'    => __('Release')
        ));

        $stabilityOptions = Mage::getModel('Magento_Connect_Model_Extension')->getStabilityOptions();
        $fieldset->addField('version', 'text', array(
            'name'      => 'version',
            'label'     => __('Release Version'),
            'required'  => true,
        ));

        $fieldset->addField('stability', 'select', array(
            'name'      => 'stability',
            'label'     => __('Release Stability'),
            'options'   => $stabilityOptions,
        ));

        $fieldset->addField('notes', 'textarea', array(
            'name'      => 'notes',
            'label'     => __('Notes'),
            'style'     => 'height:300px;',
            'required'  => true,
        ));

        $form->setValues($this->getData());
        $this->setForm($form);

        return $this;
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Release Info');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Release Info');
    }
}
