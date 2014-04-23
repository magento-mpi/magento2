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

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Catalog\Model\Product;
use Magento\Pricing\Adjustment\CalculatorInterface;

/**
 * Configured price model
 */
class ConfiguredPrice extends FinalPrice implements ConfiguredPriceInterface
{
    /**
     * Price type configured
     */
    const PRICE_CODE = self::CONFIGURED_PRICE_CODE;

    /**
     * @var null|ItemInterface
     */
    protected $item;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param ItemInterface $item
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        ItemInterface $item = null
    ) {
        $this->item = $item;
        parent::__construct($saleableItem, $quantity, $calculator);
    }

    /**
     * @param ItemInterface $item
     * @return $this
     */
    public function setItem(ItemInterface $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get value of configured options
     *
     * @return array
     */
    protected function getOptionsValue()
    {
        $product = $this->item->getProduct();
        $value = 0.;
        $optionIds = $this->item->getOptionByCode('option_ids');
        if ($optionIds) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    /** @var \Magento\Wishlist\Model\Item\Option $itemOption */
                    $itemOption = $this->item->getOptionByCode('option_' . $option->getId());
                    /** @var $group \Magento\Catalog\Model\Product\Option\Type\DefaultType */
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItem($this->item)
                        ->setConfigurationItemOption($itemOption);
                    $value += $group->getOptionPrice($itemOption->getValue(), $this->value);
                }
            }
        }
        return $value;
    }

    /**
     * Price value of product with configured options
     *
     * @return bool|float
     */
    public function getValue()
    {
        return $this->item ? parent::getValue() + $this->getOptionsValue() : parent::getValue();
    }
}
