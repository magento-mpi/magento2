<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form Type Edit Form Block
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Retrieve current form type instance
     *
     * @return \Magento\Eav\Model\Form\Type
     */
    protected function _getFormType()
    {
        return $this->_coreRegistry->registry('current_form_type');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype\Edit\Form
     */
    protected function _prepareForm()
    {
        $editMode = $this->_coreRegistry->registry('edit_mode');
        if ($editMode == 'edit') {
            $saveUrl = $this->getUrl('*/*/save');
            $showNew = false;
        } else {
            $saveUrl = $this->getUrl('*/*/create');
            $showNew = true;
        }
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $saveUrl,
                'method'    => 'post',
            ))
        );

        if ($showNew) {
            $fieldset = $form->addFieldset('base_fieldset', array(
                'legend' => __('General Information'),
                'class'  => 'fieldset-wide'
            ));

            $options = $this->_getFormType()->getCollection()->toOptionArray();
            array_unshift($options, array(
                'label' => __('-- Please Select --'),
                'value' => ''
            ));
            $fieldset->addField('type_id', 'select', array(
                'name'      => 'type_id',
                'label'     => __('Based On'),
                'title'     => __('Based On'),
                'required'  => true,
                'values'    => $options
            ));

            $fieldset->addField('label', 'text', array(
                'name'      => 'label',
                'label'     => __('Form Label'),
                'title'     => __('Form Label'),
                'required'  => true,
            ));

            /** @var $label \Magento\Core\Model\Theme\Label */
            $label = \Mage::getModel('Magento\Core\Model\Theme\Label');
            $options = $label->getLabelsCollection();
            array_unshift($options, array(
                'label' => __('All Themes'),
                'value' => ''
            ));
            $fieldset->addField('theme', 'select', array(
                'name'      => 'theme',
                'label'     => __('For Theme'),
                'title'     => __('For Theme'),
                'values'    => $options
            ));

            $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => __('Store View'),
                'title'     => __('Store View'),
                'required'  => true,
                'values'    => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(false, true)
            ));

            $form->setValues($this->_getFormType()->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
