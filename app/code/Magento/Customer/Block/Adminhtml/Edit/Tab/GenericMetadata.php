<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

/**
 * Generic block that uses customer metatdata attributes.
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class GenericMetadata extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        array $data = array()
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Set Fieldset to Form
     *
     * @param \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[] $attributes attributes that are to be added
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     * @return void
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = array())
    {
        $this->_addElementTypes($fieldset);

        foreach ($attributes as $attribute) {
            // Note, ignoring whether its visible or not,
            if (($inputType = $attribute->getFrontendInput()) && !in_array(
                $attribute->getAttributeCode(),
                $exclude
            ) && ('media_image' != $inputType || $attribute->getAttributeCode() == 'image')
            ) {

                $fieldType = $inputType;
                $element = $fieldset->addField(
                    $attribute->getAttributeCode(),
                    $fieldType,
                    array(
                        'name' => $attribute->getAttributeCode(),
                        'label' => __($attribute->getFrontendLabel()),
                        'class' => $attribute->getFrontendClass(),
                        'required' => $attribute->isRequired(),
                        'note' => $attribute->getNote()
                    )
                );

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                $this->_applyTypeSpecificConfigCustomer($inputType, $element, $attribute);
            }
        }
    }

    /**
     * Apply configuration specific for different element type
     *
     * @param string $inputType
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute
     * @return void
     */
    protected function _applyTypeSpecificConfigCustomer(
        $inputType,
        $element,
        \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute
    ) {
        switch ($inputType) {
            case 'select':
                $element->setValues($this->_getAttributeOptionsArray($attribute));
                break;
            case 'multiselect':
                $element->setValues($this->_getAttributeOptionsArray($attribute));
                $element->setCanBeEmpty(true);
                break;
            case 'date':
                $element->setImage($this->getViewFileUrl('images/grid-cal.gif'));
                $element->setDateFormat($this->_localeDate->getDateFormatWithLongYear());
                break;
            case 'multiline':
                $element->setLineCount($attribute->getMultilineCount());
                break;
            default:
                break;
        }
    }

    /**
     * @param \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute
     * @return array
     */
    protected function _getAttributeOptionsArray(\Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute)
    {
        $options = $attribute->getOptions();
        $result = array();
        foreach ($options as $option) {
            $result[] = $this->dataObjectProcessor->buildOutputDataArray($option, get_class($option));
        }
        return $result;
    }
}
