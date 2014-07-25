<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data;

use Magento\ConfigurableProduct\Service\V1\Data\Option;
use Magento\ConfigurableProduct\Service\V1\Data\OptionBuilder;
use Magento\ConfigurableProduct\Service\V1\Data\Option\ValueBuilder;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

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
     * @param OptionBuilder $optionBuilder
     * @param ValueBuilder $valueBuilder
     */
    public function __construct(
        OptionBuilder $optionBuilder,
        ValueBuilder $valueBuilder
    ) {
        $this->optionBuilder = $optionBuilder;
        $this->valueBuilder = $valueBuilder;
    }

    /**
     * Convert configurable attribute to option service object
     *
     * @param Attribute $configurableAttribute
     * @return \Magento\ConfigurableProduct\Service\V1\Data\Option
     */
    public function convert(Attribute $configurableAttribute)
    {
        $data = [
            Option::ID => $configurableAttribute->getId(),
            Option::ATTRIBUTE_ID => $configurableAttribute->getAttributeId(),
            Option::LABEL => $configurableAttribute->getLabel(),
            Option::POSITION => $configurableAttribute->getPosition(),
            Option::USE_DEFAULT => $configurableAttribute->getData('use_default'),
            Option::VALUES => null
        ];

        return $this->optionBuilder->populateWithArray($data)->create();
    }
}
