<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\Reader;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValueBuilder;
use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;
use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ReaderInterface;

class DefaultReader implements ReaderInterface
{
    /**
     * @var OptionValueBuilder
     */
    protected $valueBuilder;

    /**
     * @param OptionValueBuilder $valueBuilder
     */
    public function __construct(OptionValueBuilder $valueBuilder)
    {
        $this->valueBuilder = $valueBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function read(\Magento\Catalog\Model\Product\Option $option)
    {
        $fields = [
            OptionValue::PRICE => $option->getPrice(),
            OptionValue::PRICE_TYPE => $option->getPriceType(),
            OptionValue::SKU => $option->getSku()
        ];
        $fields = array_merge($fields, $this->getCustomAttributes($option));
        $value = $this->valueBuilder->populateWithArray($fields)->create();
        return [$value];
    }

    /**
     * Get custom attributes
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function getCustomAttributes(\Magento\Catalog\Model\Product\Option $option)
    {
        return [];
    }
}
