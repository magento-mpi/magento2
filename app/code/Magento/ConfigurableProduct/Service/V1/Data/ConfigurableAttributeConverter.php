<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Data;


use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

/**
 * Class ConfigurableAttributeConverter
 *
 * @package Magento\ConfigurableProduct\Service\V1\Data
 */
class ConfigurableAttributeConverter
{
    private $valueBuilder;
    /** @var \Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttributeBuilder */
    private $configurableAttributeBuilder;

    /**
     * @param ConfigurableAttributeBuilder $configurableAttributeBuilder
     * @param ConfigurableAttribute\ValueBuilder $valueBuilder
     */
    public function __construct(
        ConfigurableAttributeBuilder $configurableAttributeBuilder,
        ConfigurableAttribute\ValueBuilder $valueBuilder
    ) {
        $this->configurableAttributeBuilder = $configurableAttributeBuilder;
        $this->valueBuilder = $valueBuilder;
    }

    /**
     * @param Attribute $attribute
     * @return \Magento\Framework\Service\Data\AbstractObject|null
     */
    public function createDataFromModel(Attribute $attribute)
    {
        $dto = null;
        $values = $this->getPriceValues($attribute->getData('prices'));

        $dto = $this->configurableAttributeBuilder->populateWithArray($attribute->getData())
            ->setValues($values)
            ->create();
        return $dto;
    }

    /**
     * @param $prices
     * @return array
     */
    private function getPriceValues($prices)
    {
        $values = [];
        foreach ((array)$prices as $price) {
            $values[] = $this->valueBuilder->populateWithArray($price)
                ->setIndex($price['value_index'])
                ->setPrice($price['pricing_value'])
                ->setPriceIsPercent($price['is_percent'])
                ->create();
        }
        return $values;
    }
} 