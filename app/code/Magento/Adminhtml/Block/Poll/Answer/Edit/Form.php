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
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Poll_Answer_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();

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

        $form->setValues(Mage::registry('answer_data')->getData());
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/*/save', array('id' => Mage::registry('answer_data')->getAnswerId())));
        $this->setForm($form);
    }
}
