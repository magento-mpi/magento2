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
class ConfiguredPrice extends FinalPrice
{
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
}
