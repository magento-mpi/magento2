<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory;
use Magento\ConfigurableProduct\Service\V1\Data\Option\ValueBuilder;
use Magento\ConfigurableProduct\Service\V1\Data\Option;
use Magento\ConfigurableProduct\Service\V1\Data\Option\ValueConverter;

class OptionConverter
{
    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\OptionBuilder
     */
    protected $optionBuilder;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\Option\ValueBuilder
     */
    protected $valueBuilder;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory
     */
    private $attributeFactory;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\Option\ValueConverter
     */
    private $valueConverter;

    /**
     * @param OptionBuilder $optionBuilder
     * @param ValueBuilder $valueBuilder
     * @param AttributeFactory $attributeFactory
     * @param ValueConverter $valueConverter
     */
    public function __construct(
        OptionBuilder $optionBuilder,
        ValueBuilder $valueBuilder,
        AttributeFactory $attributeFactory,
        ValueConverter $valueConverter
    ) {
        $this->optionBuilder = $optionBuilder;
        $this->valueBuilder = $valueBuilder;
        $this->attributeFactory = $attributeFactory;
        $this->valueConverter = $valueConverter;
    }

    /**
     * Convert configurable attribute to option service object
     *
     * @param Attribute $configurableAttribute
     * @return \Magento\ConfigurableProduct\Service\V1\Data\Option
     */
    public function convertFromModel(Attribute $configurableAttribute)
    {
        $values = [];
        $prices = $configurableAttribute->getPrices();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $values[] = $this->valueBuilder
                    ->setIndex($price['value_index'])
                    ->setPrice($price['pricing_value'])
                    ->setPercent($price['is_percent'])
                    ->create();
            }
        }
        $data = [
            Option::ID => $configurableAttribute->getId(),
            Option::ATTRIBUTE_ID => $configurableAttribute->getAttributeId(),
            Option::LABEL => $configurableAttribute->getLabel(),
            Option::POSITION => $configurableAttribute->getPosition(),
            Option::USE_DEFAULT => $configurableAttribute->getData('use_default'),
            Option::VALUES => $values
        ];

        return $this->optionBuilder->populateWithArray($data)->create();
    }

    /**
     * @param Option $option
     * @param Attribute $configurableAttribute
     * @return Attribute
     */
    public function getModelFromData(Option $option, Attribute $configurableAttribute)
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $returnConfigurableAttribute */
        $returnConfigurableAttribute = $this->attributeFactory->create();
        $returnConfigurableAttribute->setData($configurableAttribute->getData());
        $returnConfigurableAttribute->addData($option->__toArray());
        $returnConfigurableAttribute->setId($configurableAttribute->getId());
        $returnConfigurableAttribute->setAttributeId($configurableAttribute->getAttributeId());
        $returnConfigurableAttribute->setValues($configurableAttribute->getPrices());

        $values = $option->getValues();
        if (!is_null($values)) {
            $prices = [];
            foreach ($values as $value) {
                $prices[] = $this->valueConverter->convertArrayFromData($value);
            }
            $returnConfigurableAttribute->setValues($prices);
        }

        return $returnConfigurableAttribute;
    }
}
