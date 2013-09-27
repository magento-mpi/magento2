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

class Magento_Adminhtml_Block_Poll_Edit_Tab_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_adminhtmlSession;

    /**
     * @param Magento_Backend_Model_Session $adminhtmlSession
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Model_Session $adminhtmlSession,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->_storeManager = $context->getStoreManager();
        $this->_systemStore = $systemStore;
        $this->_adminhtmlSession = $adminhtmlSession;
    }

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

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('store_ids', 'multiselect', array(
                'label'     => __('Visible In'),
                'required'  => true,
                'name'      => 'store_ids[]',
                'values'    => $this->_systemStore->getStoreValuesForForm(),
                'value'     => $this->_coreRegistry->registry('poll_data')->getStoreIds()
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_ids', 'hidden', array(
                'name'      => 'store_ids[]',
                'value'     => $this->_storeManager->getStore(true)->getId()
            ));
            $this->_coreRegistry->registry('poll_data')->setStoreIds($this->_storeManager->getStore(true)->getId());
        }


        if ($this->_adminhtmlSession->getPollData()) {
            $form->setValues($this->_adminhtmlSession->getPollData());
            $this->_adminhtmlSession->setPollData(null);
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
