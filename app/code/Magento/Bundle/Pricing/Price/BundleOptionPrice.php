<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Bundle\Model\Product\Price;
use Magento\Pricing\Adjustment\Calculator;
use Magento\Pricing\Object\SaleableInterface;

/**
 * Bundle option price model
 */
class BundleOptionPrice extends RegularPrice implements BundleOptionPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_BUNDLE_OPTION;

    /**
     * @var array
     */
    protected $priceOptions;

    /**
     * @var BundleSelectionFactory
     */
    protected $selectionFactory;

    /**
     * @var float|bool|null
     */
    protected $maximalPrice;

    protected $amountData;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param Calculator $calculator
     * @param BundleSelectionFactory $bundleSelectionFactory
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        Calculator $calculator,
        BundleSelectionFactory $bundleSelectionFactory
    ) {
        $this->selectionFactory = $bundleSelectionFactory;
        parent::__construct($salableItem, $quantity, $calculator);
        $this->processOptions();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool|float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get Options with attached Selections collection
     *
     * @return \Magento\Bundle\Model\Resource\Option\Collection
     */
    public function getOptions()
    {
        if (null !== $this->priceOptions) {
            return $this->priceOptions;
        }
        $this->salableItem->getTypeInstance()->setStoreFilter($this->salableItem->getStoreId(), $this->salableItem);

        $optionCollection = $this->salableItem->getTypeInstance()->getOptionsCollection($this->salableItem);

        $selectionCollection = $this->salableItem->getTypeInstance()->getSelectionsCollection(
            $this->salableItem->getTypeInstance()->getOptionsIds($this->salableItem),
            $this->salableItem
        );

        $this->priceOptions = $optionCollection->appendSelections($selectionCollection, false, false);
        return $this->priceOptions;
    }

    /**
     * Calculate all options
     */
    protected function processOptions()
    {
        $this->salableItem->setQty($this->quantity);
        $minimalPrice = $maximalPrice = false;
        $this->amountData = [];
        /* @var $option \Magento\Bundle\Model\Option */
        foreach ($this->getOptions() as $option) {
            if (!$option->getSelections()) {
                continue;
            }
            $selectionPrices = [];
            foreach ($option->getSelections() as $selection) {
                /* @var $selection \Magento\Bundle\Model\Selection */
                if (!$selection->isSalable()) {
                    /**
                     * @todo CatalogInventory Show out of stock Products
                     */
                    continue;
                }

                $selectionPrice = $this->selectionFactory
                    ->create($this->salableItem, $selection, $selection->getSelectionQty())
                    ->getValue();
                $selectionPrices[] = $selectionPrice;

                // AMOUNT CALCULATION START
                if ($this->salableItem->getPriceType() == Price::PRICE_TYPE_FIXED) {
                    $item = $this->salableItem;
                } else {
                    $item = $selection;
                }
                $this->amountData[] = $this->calculator->getAmount($selectionPrice, $item);
                // AMOUNT CALCULATION END
            }
            if (count($selectionPrices)) {
                $selMinPrice = min($selectionPrices);
                if ($option->getRequired()) {
                    $minimalPrice += $selMinPrice;
                }

                if ($option->isMultiSelection()) {
                    $maximalPrice += array_sum($selectionPrices);
                } else {
                    $maximalPrice += max($selectionPrices);
                }
            }
        }
        $this->value = $minimalPrice;
        $this->maximalPrice = $maximalPrice;
    }

    /**
     * Getter for maximal price of options
     *
     * @return bool|float
     */
    public function getMaxValue()
    {
        return $this->maximalPrice;
    }
}
