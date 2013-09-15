<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll edit form
 */

namespace Magento\Adminhtml\Block\Poll\Edit\Tab;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('poll_form', array('legend'=>__('Poll information')));
        $fieldset->addField('poll_title', 'text', array(
            'label'     => __('Poll Question'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'poll_title',
        ));

        $fieldset->addField('closed', 'select', array(
            'label'     => __('Status'),
            'name'      => 'closed',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => __('Closed'),
                ),

                array(
                    'value'     => 0,
                    'label'     => __('Open'),
                ),
            ),
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_ids', 'multiselect', array(
                'label'     => __('Visible In'),
                'required'  => true,
                'name'      => 'store_ids[]',
                'values'    => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(),
                'value'     => $this->_coreRegistry->registry('poll_data')->getStoreIds()
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_ids', 'hidden', array(
                'name'      => 'store_ids[]',
                'value'     => \Mage::app()->getStore(true)->getId()
            ));
            $this->_coreRegistry->registry('poll_data')->setStoreIds(\Mage::app()->getStore(true)->getId());
        }


        if (\Mage::getSingleton('Magento\Adminhtml\Model\Session')->getPollData()) {
            $form->setValues(\Mage::getSingleton('Magento\Adminhtml\Model\Session')->getPollData());
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setPollData(null);
        } elseif($this->_coreRegistry->registry('poll_data')) {
            $form->setValues($this->_coreRegistry->registry('poll_data')->getData());

            $fieldset->addField('was_closed', 'hidden', array(
                'name'      => 'was_closed',
                'no_span'   => true,
                'value'     => $this->_coreRegistry->registry('poll_data')->getClosed()
            ));
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
