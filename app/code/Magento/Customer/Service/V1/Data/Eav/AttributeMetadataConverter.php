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
            ->setInputFilter($attribute->getInputFilter())
            ->setStoreLabel($attribute->getStoreLabel())
            ->setValidationRules($validationRules)
            ->setVisible($attribute->getIsVisible())
            ->setRequired($attribute->getIsRequired())
            ->setMultilineCount($attribute->getMultilineCount())
            ->setDataModel($attribute->getDataModel())
            ->setOptions($options)
            ->setFrontendClass($attribute->getFrontend()->getClass())
            ->setFrontendLabel($attribute->getFrontendLabel())
            ->setBackendType($attribute->getBackendType())
            ->setNote($attribute->getNote())
            ->setIsSystem($attribute->getIsSystem())
            ->setIsUserDefined($attribute->getIsUserDefined())
            ->setSortOrder($attribute->getSortOrder());

        return $this->_attributeMetadataBuilder->create();
    }
}
