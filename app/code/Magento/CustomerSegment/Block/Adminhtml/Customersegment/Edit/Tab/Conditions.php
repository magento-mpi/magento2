<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Conditions tab of customer segment configuration
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions
    extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset
     */
    protected $_fieldset;

    /**
     * @var Magento_Rule_Block_Conditions
     */
    protected $_conditions;

    /**
     * @param Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset $fieldset
     * @param Magento_Rule_Block_Conditions $conditions
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset $fieldset,
        Magento_Rule_Block_Conditions $conditions,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_fieldset = $fieldset;
        $this->_conditions = $conditions;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare conditions form
     *
     * @return Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_customer_segment');

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('segment_');
        $params = array('apply_to' => $model->getApplyTo());
        $url = $this->getUrl('*/customersegment/newConditionHtml/form/segment_conditions_fieldset', $params);

        $renderer = $this->_fieldset->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($url);

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend' => __('Conditions'),
            'class' => 'fieldset',
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer($this->_conditions);

        if (Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS_AND_REGISTERED == $model->getApplyTo()) {
            $fieldset->addField('conditions-label', 'label', array(
                'note' => __('* applicable to visitors and registered customers'),
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
