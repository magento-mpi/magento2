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
 * Adminhtml poll answer edit form
 */

class Magento_Adminhtml_Block_Poll_Answer_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('edit_answer_form', array('legend' => __('Edit Poll Answer')));

        $fieldset->addField('answer_title', 'text', array(
                    'name'      => 'answer_title',
                    'title'     => __('Answer Title'),
                    'label'     => __('Answer Title'),
                    'required'  => true,
                    'class'     => 'required-entry',
                )
        );

        $fieldset->addField('votes_count', 'text', array(
                    'name'      => 'votes_count',
                    'title'     => __('Votes Count'),
                    'label'     => __('Votes Count'),
                    'class'     => 'validate-not-negative-number'
                )
        );

        $fieldset->addField('poll_id', 'hidden', array(
                    'name'      => 'poll_id',
                    'no_span'   => true,
                )
        );

        $form->setValues($this->_coreRegistry->registry('answer_data')->getData());
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/*/save', array('id' => $this->_coreRegistry->registry('answer_data')->getAnswerId())));
        $this->setForm($form);
    }
}
