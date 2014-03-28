<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Pricing\Price;

/**
 * Class OptionPrice
 *
 * @package Magento\Catalog\Pricing\Price
 */
class OptionPrice extends \Magento\Catalog\Pricing\Price\RegularPrice
{
    /**
     * Price model code
     */
    const PRICE_TYPE_CUSTOM_OPTION = 'custom_option';

    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_CUSTOM_OPTION;

    /**
     * @var bool|false|float|null
     */
    protected $value;

    /**
     * @var array
     */
    protected $priceOptions;

    /**
     * @return bool|false|float|null
     */
    public function getValue()
    {
        if (null !== $this->value) {
            return $this->value;
        }
        $this->value = false;
        $optionIds = $this->salableItem->getCustomOption('option_ids');
        if ($optionIds) {
            $this->value = 0.;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $this->salableItem->getOptionById($optionId)) {
                    $confItemOption = $this->salableItem->getCustomOption('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);
                    $this->value += $group->getOptionPrice($confItemOption->getValue(), $this->value);
                }
            }
        }
        return $this->value;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (null !== $this->priceOptions) {
            return $this->priceOptions;
        }
        $this->priceOptions = [];
        $options = $this->salableItem->getOptions();
        if ($options) {
            /** @var $optionItem \Magento\Catalog\Model\Product\Option */
            foreach ($options as $optionItem) {
                /** @var $optionValue \Magento\Catalog\Model\Product\Option\Value */
                foreach ($optionItem->getValues() as $optionValue) {
                    $price = $optionValue->getPrice($optionValue->getPriceType() == 'percent');
                    $this->priceOptions[$optionValue->getId()][$price] = [
                        'base_amount' => $price,
                        'adjustment' => $this->getDisplayValue($price)
                    ];
                }
            }
        }
        return $this->priceOptions;
    }
}
