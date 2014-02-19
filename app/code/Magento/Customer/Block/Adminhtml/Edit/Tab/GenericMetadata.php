<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab;
use Magento\Customer\Service\V1\Dto\Eav\Option;

/**
 * Generic block that uses customer metatdata attributes.
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class GenericMetadata extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Set Fieldset to Form
     *
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata[] $attributes attributes that are to be added
     * @param \Magento\Data\Form\Element\Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = array())
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            // Note, ignoring whether its visible or not,
            if (($inputType = $attribute->getFrontendInputType())
                && !in_array($attribute->getAttributeCode(), $exclude)
                && (('media_image' != $inputType) || ($attribute->getAttributeCode() == 'image'))
            ) {

                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontendInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $element = $fieldset->addField(
                    $attribute->getAttributeCode(),
                    $fieldType,
                    array(
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => __($attribute->getFrontendLabel()),
                        'class'     => $attribute->getFrontendClass(),
                        'required'  => $attribute->isRequired(),
                        'note'      => $attribute->getNote(),
                    )
                )
                    ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                $this->_applyTypeSpecificConfigCustomer($inputType, $element, $attribute);
            }
        }
    }

    /**
     * Apply configuration specific for different element type
     *
     * @param string $inputType
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute
     */
    protected function _applyTypeSpecificConfigCustomer(
        $inputType,
        $element,
        \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute
    ) {
        switch ($inputType) {
            case 'select':
                $element->setValues($this->convertOptionsToArray($attribute->getOptions()));
                break;
            case 'multiselect':
                $element->setValues($attribute->getSource()->getAllOptions());
                $element->setCanBeEmpty(true);
                break;
            case 'date':
                $element->setImage($this->getViewFileUrl('images/grid-cal.gif'));
                $element->setDateFormat($this->_locale->getDateFormatWithLongYear());
                break;
            case 'multiline':
                $element->setLineCount($attribute->getMultilineCount());
                break;
            default:
                break;
        }
    }

    /**
     * Converts an array of Option DTOs into an array of value => label pairs
     *
     * @param Option[] $options
     * @return array
     */
    private function convertOptionsToArray(array $options)
    {
        $result = [];
        foreach ($options as $option) {
            $result[$option->getValue()] = $option->getLabel();
        }
        return $result;
    }
}
