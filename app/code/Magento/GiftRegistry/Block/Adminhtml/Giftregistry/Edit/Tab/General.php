<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_General
    extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Backend_Model_Config_Source_Yesno
     */
    protected $sourceYesNo;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Backend_Model_Config_Source_Yesno $sourceYesNo
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Backend_Model_Config_Source_Yesno $sourceYesNo,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
        $this->sourceYesNo = $sourceYesNo;
    }

    /**
     * Return current gift registry type instance
     *
     * @return Magento_GiftRegistry_Model_Type
     */
    public function getType()
    {
        return $this->_coreRegistry->registry('current_giftregistry_type');
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getLayout()->hasElement($this->getNameInLayout() . '_element')) {
            $this->getLayout()->unsetElement($this->getNameInLayout() . '_element');
        }
        Magento_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Form_Renderer_Element',
                $this->getNameInLayout() . '_element'
            )
        );
    }

    /**
     * Prepare general properties form
     *
     * @return Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setFieldNameSuffix('type');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'  => __('General Information')
        ));

        if ($this->getType()->getId()) {
            $fieldset->addField('type_id', 'hidden', array(
                'name' => 'type_id'
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => __('Code'),
            'required' => true,
            'class'    => 'validate-code'
        ));

        $fieldset->addField('label', 'text', array(
            'name'     => 'label',
            'label'    => __('Label'),
            'required' => true,
            'scope'    => 'store'
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'     => 'sort_order',
            'label'    => __('Sort Order'),
            'scope'    => 'store'
        ));

        $fieldset->addField('is_listed', 'select', array(
            'label'    => __('Is Listed'),
            'name'     => 'is_listed',
            'values'   => $this->sourceYesNo->toOptionArray(),
            'scope'    => 'store'
        ));

        $form->setValues($this->getType()->getData());
        $this->setForm($form);
        $form->setDataObject($this->getType());

        return parent::_prepareForm();
    }
}
