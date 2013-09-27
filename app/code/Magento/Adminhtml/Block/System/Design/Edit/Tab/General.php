<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Adminhtml_Block_System_Design_Edit_Tab_General extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Core_Model_Theme_LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @param Magento_Core_Model_Theme_LabelFactory $labelFactory
     * @param Magento_Backend_Model_Session $backendSession
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Theme_LabelFactory $labelFactory,
        Magento_Backend_Model_Session $backendSession,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_labelFactory = $labelFactory;
        $this->_backendSession = $backendSession;
        $this->_systemStore = $systemStore;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return Magento_Adminhtml_Block_System_Design_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('general', array(
            'legend' => __('General Settings'))
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => __('Store'),
                'title'    => __('Store'),
                'values'   => $this->_systemStore->getStoreValuesForForm(),
                'name'     => 'store_id',
                'required' => true,
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => $this->_storeManager->getStore(true)->getId(),
            ));
        }

        /** @var $label Magento_Core_Model_Theme_Label */
        $label = $this->_labelFactory->create();
        $options = $label->getLabelsCollection(__('-- Please Select --'));
        $fieldset->addField('design', 'select', array(
            'label'    => __('Custom Design'),
            'title'    => __('Custom Design'),
            'values'   => $options,
            'name'     => 'design',
            'required' => true,
        ));

        $dateFormat = $this->_locale->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('date_from', 'date', array(
            'label'    => __('Date From'),
            'title'    => __('Date From'),
            'name'     => 'date_from',
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            //'required' => true,
        ));
        $fieldset->addField('date_to', 'date', array(
            'label'    => __('Date To'),
            'title'    => __('Date To'),
            'name'     => 'date_to',
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            //'required' => true,
        ));

        $formData = $this->_backendSession->getDesignData(true);
        if (!$formData) {
            $formData = $this->_coreRegistry->registry('design')->getData();
        } else {
            $formData = $formData['design'];
        }

        $form->addValues($formData);
        $form->setFieldNameSuffix('design');
        $this->setForm($form);
    }

}
