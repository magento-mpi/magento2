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
class Magento_Adminhtml_Block_Poll_Answer_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_createForm();

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
