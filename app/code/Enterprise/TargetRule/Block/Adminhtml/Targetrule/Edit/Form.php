<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{

    /**
     * Adminhtml data
     *
     * @var Magento_Adminhtml_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('enterprise_targetrule_form');
        $this->setTitle(__('Rule Information'));
    }

    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array('id' => 'edit_form',
            'action' => $this->_adminhtmlData->getUrl('*/*/save'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }


}
