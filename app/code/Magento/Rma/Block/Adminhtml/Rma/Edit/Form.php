<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Adminhtml_Rma_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
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

    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = $this->_createForm(
            array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $model = $this->_coreRegistry->registry('current_rma');

        if ($model) {
            if ($model->getId()) {
                $form->addField('entity_id', 'hidden', array(
                    'name' => 'entity_id',
                ));
                $form->setValues($model->getData());
            }

            $this->_order = ($model->getOrderId());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
