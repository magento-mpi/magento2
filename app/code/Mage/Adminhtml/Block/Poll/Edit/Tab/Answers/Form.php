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
 * Adminhtml poll edit answer tab form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Poll_Edit_Tab_Answers_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('add_answer_form', array('legend' => __('Add New Answer')));

        $fieldset->addField('answer_title', 'text', array(
                    'name'      => 'answer_title',
                    'title'     => __('Answer Title'),
                    'label'     => __('Answer Title'),
                    'maxlength' => '255',
                    'no_span'   => true,
                )
        );

        $fieldset->addField('poll_id', 'hidden', array(
                    'name'      => 'poll_id',
                    'no_span'   => true,
                    'value'     => $this->getRequest()->getParam('id'),
                )
        );

        $fieldset->addField('add_button', 'note', array(
                    'text' => $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                                    ->setData(array(
                                        'label'     => __('Add Answer'),
                                        'onclick'   => 'answers.add();',
                                        'class'     => 'add',
                                    ))->toHtml(),
                    'no_span'   => true,
                )
        );

        $this->setForm($form);
    }
}
