<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data\Eav;

/**
 * Converter for AttributeMetadata
 */
class AttributeMetadataConverter
{
    /**
     * @var OptionBuilder
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
     * @param OptionBuilder $optionBuilder
     * @param ValidationRuleBuilder $validationRuleBuilder
     * @param AttributeMetadataBuilder $attributeMetadataBuilder
     */
    public function __construct(
        OptionBuilder $optionBuilder,
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
     * @return AttributeMetadata
     */
    public function createMetadataAttribute($attribute)
    {
        $options = [];
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $options[] = $this->_optionBuilder->setLabel($option['label'])
                    ->setValue($option['value'])
                    ->create();
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
            ->setIsSystem((boolean)$attribute->getIsSystem())
            ->setIsUserDefined((boolean)$attribute->getIsUserDefined())
            ->setBackendType($attribute->getBackendType())
            ->setSortOrder((int)$attribute->getSortOrder());

        return $this->_attributeMetadataBuilder->create();
    }
}
