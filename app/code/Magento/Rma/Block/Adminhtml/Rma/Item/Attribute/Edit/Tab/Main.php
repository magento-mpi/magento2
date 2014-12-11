<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit\Tab;

/**
 * RMA Item Attributes Edit Form
 */
class Main extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Rma eav
     *
     * @var \Magento\CustomAttributeManagement\Helper\Data
     */
    protected $_attributeHelper = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Eav\Helper\Data $eavData
     * @param \Magento\Backend\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory
     * @param \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig
     * @param \Magento\CustomAttributeManagement\Helper\Data $attributeHelper
     * @param \Magento\Rma\Helper\Eav $rmaEav
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Helper\Data $eavData,
        \Magento\Backend\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory,
        \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig,
        \Magento\CustomAttributeManagement\Helper\Data $attributeHelper,
        \Magento\Rma\Helper\Eav $rmaEav,
        array $data = []
    ) {
        $this->_attributeHelper = $attributeHelper;
        $this->_rmaEav = $rmaEav;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $eavData,
            $yesnoFactory,
            $inputTypeFactory,
            $attributeConfig,
            $data
        );
    }

    /**
     * Preparing global layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $result = parent::_prepareLayout();
        $renderer = $this->getLayout()->getBlock('fieldset_element_renderer');
        if ($renderer instanceof \Magento\Framework\Data\Form\Element\Renderer\RendererInterface) {
            \Magento\Framework\Data\Form::setFieldsetElementRenderer($renderer);
        }

        return $result;
    }

    /**
     * Adding customer form elements for edit form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $attribute = $this->getAttributeObject();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->removeField('frontend_class');
        $fieldset->removeField('is_unique');

        // update Input Types
        $element = $form->getElement('frontend_input');
        $element->setValues($this->_attributeHelper->getFrontendInputOptions());
        $element->setLabel(__('Input Type'));
        $element->setRequired(true);

        // add limitation to attribute code
        // customer attribute code can have prefix "rma_item_" and its length must be max length minus prefix length
        $element = $form->getElement('attribute_code');
        $element->setNote(
            __(
                'For internal use. Must be unique with no spaces. Maximum length of attribute code must be less than %1 symbols',
                \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
            )
        );

        $fieldset->addField(
            'multiline_count',
            'text',
            [
                'name' => 'multiline_count',
                'label' => __('Lines Count'),
                'title' => __('Lines Count'),
                'required' => true,
                'class' => 'validate-digits-range digits-range-2-20',
                'note' => __('Valid range 2-20')
            ],
            'frontend_input'
        );

        $fieldset->addField(
            'input_validation',
            'select',
            [
                'name' => 'input_validation',
                'label' => __('Input Validation'),
                'title' => __('Input Validation'),
                'values' => ['' => __('None')]
            ],
            'default_value_textarea'
        );

        $fieldset->addField(
            'min_text_length',
            'text',
            [
                'name' => 'min_text_length',
                'label' => __('Minimum Text Length'),
                'title' => __('Minimum Text Length'),
                'class' => 'validate-digits'
            ],
            'input_validation'
        );

        $fieldset->addField(
            'max_text_length',
            'text',
            [
                'name' => 'max_text_length',
                'label' => __('Maximum Text Length'),
                'title' => __('Maximum Text Length'),
                'class' => 'validate-digits'
            ],
            'min_text_length'
        );

        $fieldset->addField(
            'max_file_size',
            'text',
            [
                'name' => 'max_file_size',
                'label' => __('Maximum File Size (bytes)'),
                'title' => __('Maximum File Size (bytes)'),
                'class' => 'validate-digits'
            ],
            'max_text_length'
        );

        $fieldset->addField(
            'file_extensions',
            'text',
            [
                'name' => 'file_extensions',
                'label' => __('File Extensions'),
                'title' => __('File Extensions'),
                'note' => __('Comma separated')
            ],
            'max_file_size'
        );

        $fieldset->addField(
            'max_image_width',
            'text',
            [
                'name' => 'max_image_width',
                'label' => __('Maximum Image Width (px)'),
                'title' => __('Maximum Image Width (px)'),
                'class' => 'validate-digits'
            ],
            'file_extensions'
        );

        $fieldset->addField(
            'max_image_heght',
            'text',
            [
                'name' => 'max_image_heght',
                'label' => __('Maximum Image Height (px)'),
                'title' => __('Maximum Image Height (px)'),
                'class' => 'validate-digits'
            ],
            'max_image_width'
        );

        $fieldset->addField(
            'input_filter',
            'select',
            [
                'name' => 'input_filter',
                'label' => __('Input/Output Filter'),
                'title' => __('Input/Output Filter'),
                'values' => ['' => __('None')]
            ]
        );

        /** @var $config \Magento\Backend\Model\Config\Source\Yesno */
        $config = $this->_yesnoFactory->create();
        $yesnoSource = $config->toOptionArray();

        $fieldset = $form->addFieldset('front_fieldset', ['legend' => __('Frontend Properties')]);

        $fieldset->addField(
            'is_visible',
            'select',
            [
                'name' => 'is_visible',
                'label' => __('Show on Frontend'),
                'title' => __('Show on Frontend'),
                'values' => $yesnoSource
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'class' => 'validate-digits'
            ]
        );

        $fieldset->addField(
            'used_in_forms',
            'multiselect',
            [
                'name' => 'used_in_forms',
                'label' => __('Forms to Use In'),
                'title' => __('Forms to Use In'),
                'values' => $this->_attributeHelper->getAttributeFormOptions(),
                'value' => $attribute->getUsedInForms(),
                'can_be_empty' => true,
                'required' => true,
            ]
        )->setSize(
            5
        );

        if ($attribute->getId()) {
            $elements = [];
            if ($attribute->getIsSystem()) {
                $elements = ['sort_order', 'is_visible', 'is_required'];
            }
            if (!$attribute->getIsUserDefined() && !$attribute->getIsSystem()) {
                $elements = ['sort_order'];
            }
            foreach ($elements as $elementId) {
                $form->getElement($elementId)->setDisabled(true);
            }

            $inputTypeProp = $this->_attributeHelper->getAttributeInputTypes($attribute->getFrontendInput());

            // input_filter
            if ($inputTypeProp['filter_types']) {
                $filterTypes = $this->_attributeHelper->getAttributeFilterTypes();
                $values = $form->getElement('input_filter')->getValues();
                foreach ($inputTypeProp['filter_types'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }
                $form->getElement('input_filter')->setValues($values);
            }

            // input_validation getAttributeValidateFilters
            if ($inputTypeProp['validate_filters']) {
                $filterTypes = $this->_attributeHelper->getAttributeValidateFilters();
                $values = $form->getElement('input_validation')->getValues();
                foreach ($inputTypeProp['validate_filters'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }
                $form->getElement('input_validation')->setValues($values);
            }
        }

        // apply scopes
        foreach ($this->_attributeHelper->getAttributeElementScopes() as $elementId => $scope) {
            $element = $form->getElement($elementId);
            if ($element) {
                $element->setScope($scope);
                if ($this->getAttributeObject()->getWebsite()->getId()) {
                    $element->setName('scope_' . $element->getName());
                }
            }
        }

        $this->getForm()->setDataObject($this->getAttributeObject());

        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var \Magento\Backend\Block\Widget\Form\Element\Dependence $dependenceBlock */
        $dependenceBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $this->setChild(
            'form_after',
            $dependenceBlock->addFieldMap($htmlIdPrefix . 'used_in_forms', 'used_in_forms')
                ->addFieldMap($htmlIdPrefix . 'is_visible', 'is_visible')
                ->addFieldDependence('used_in_forms', 'is_visible', '1')
        );

        return $this;
    }

    /**
     * Initialize form fileds values
     *
     * @return \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
     */
    protected function _initFormValues()
    {
        $attribute = $this->getAttributeObject();
        if ($attribute->getId() && $attribute->getValidateRules()) {
            $this->getForm()->addValues($attribute->getValidateRules());
        }
        $result = parent::_initFormValues();

        // get data using methods to apply scope
        $formValues = $this->getAttributeObject()->getData();
        foreach (array_keys($formValues) as $idx) {
            $formValues[$idx] = $this->getAttributeObject()->getDataUsingMethod($idx);
        }
        $this->getForm()->addValues($formValues);

        return $result;
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Properties');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Properties');
    }

    /**
     * Can show tab in tabs
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
