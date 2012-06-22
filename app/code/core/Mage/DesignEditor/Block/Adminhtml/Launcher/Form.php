<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design editor launcher form
 */
class Mage_DesignEditor_Block_Adminhtml_Launcher_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('adminhtml/system_design_editor/launch'),
            'target'    => '_blank'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                array('legend' => Mage::helper('Mage_DesignEditor_Helper_Data')->__('Context Information'))
            );
            $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('Mage_DesignEditor_Helper_Data')->__('Store View'),
                'title'     => Mage::helper('Mage_DesignEditor_Helper_Data')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('Mage_Core_Model_System_Store')->getStoreValuesForForm(),
            ));
        }

        $this->setForm($form);
        $form->setUseContainer(true);

        return parent::_prepareForm();
    }
}
