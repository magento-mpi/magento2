<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\Customer\Api\Data\AttributeMetadataBuilder;
use Magento\Customer\Api\Data\OptionDataBuilder;
use Magento\Customer\Api\Data\ValidationRuleBuilder;

/**
 * Converter for AttributeMetadata
 */
class AttributeMetadataConverter
{
    /**
     * @var OptionDataBuilder
     */
    private $_optionBuilder;

    /**
     * @var ValidationRuleBuilder
     */
    private $_validationRuleBuilder;

    /**
     * @var AttributeMetadataBuilder
     */
    private $_attributeMetadataBuilder;

    /**
     * Initialize the Converter
     *
     * @param OptionDataBuilder $optionBuilder
     * @param ValidationRuleBuilder $validationRuleBuilder
     * @param AttributeMetadataBuilder $attributeMetadataBuilder
     */
    public function __construct(
        OptionDataBuilder $optionBuilder,
        ValidationRuleBuilder $validationRuleBuilder,
        AttributeMetadataBuilder $attributeMetadataBuilder
    ) {
        $this->_optionBuilder = $optionBuilder;
        $this->_validationRuleBuilder = $validationRuleBuilder;
        $this->_attributeMetadataBuilder = $attributeMetadataBuilder;
    }

    /**
     * Create AttributeMetadata Data object from the Attribute Model
     *
     * @param \Magento\Customer\Model\Attribute $attribute
     * @return \Magento\Customer\Api\Data\AttributeMetadataInterface
     */
    public function createMetadataAttribute($attribute)
    {
        $options = [];
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                if (!is_array($option['value'])) {
                    $this->_optionBuilder->setValue($option['value']);
                } else {
                    $optionArray = [];
                    foreach ($option['value'] as $optionArrayValues) {
                        $optionArray[] = $this->_optionBuilder->populateWithArray($optionArrayValues)->create();
                    }
                    $this->_optionBuilder->setOptions($optionArray);
                }
                $this->_optionBuilder->setLabel($option['label']);
                $options[] = $this->_optionBuilder->create();
            }
        }
        $validationRules = [];
        foreach ($attribute->getValidateRules() as $name => $value) {
            $validationRules[] = $this->_validationRuleBuilder->setName($name)
                ->setValue($value)
                ->create();
        }

        $this->_attributeMetadataBuilder->setAttributeCode($attribute->getAttributeCode())
            ->setFrontendInput($attribute->getFrontendInput())
            ->setInputFilter((string)$attribute->getInputFilter())
            ->setStoreLabel($attribute->getStoreLabel())
            ->setValidationRules($validationRules)
            ->setVisible((boolean)$attribute->getIsVisible())
            ->setRequired((boolean)$attribute->getIsRequired())
            ->setMultilineCount((int)$attribute->getMultilineCount())
            ->setDataModel((string)$attribute->getDataModel())
            ->setOptions($options)
            ->setFrontendClass($attribute->getFrontend()->getClass())
            ->setFrontendLabel($attribute->getFrontendLabel())
            ->setNote((string)$attribute->getNote())
            ->setSystem((boolean)$attribute->getIsSystem())
            ->setUserDefined((boolean)$attribute->getIsUserDefined())
            ->setBackendType($attribute->getBackendType())
            ->setSortOrder((int)$attribute->getSortOrder());

        return $this->_attributeMetadataBuilder->create();
    }
}
