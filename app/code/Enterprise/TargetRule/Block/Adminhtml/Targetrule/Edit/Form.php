<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Form extends Magento_Backend_Block_Widget_Form
{

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData = null;

    /**
     * @var Magento_Data_FormFactory|null
     */
    protected $_formFactory = null;

    /**
     * @param Magento_Data_FormFactory $formFactory
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Data_FormFactory $formFactory,
        Magento_Backend_Helper_Data $backendData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_backendData = $backendData;
        $this->_formFactory = $formFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('enterprise_targetrule_form');
        $this->setTitle(__('Rule Information'));
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(array('id' => 'edit_form',
            'action' => $this->_backendData->getUrl('*/*/save'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
