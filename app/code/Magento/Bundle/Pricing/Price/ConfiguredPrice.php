<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price as CatalogPrice;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Pricing\Amount\AmountFactory;
use Magento\Bundle\Pricing\BundleOptionService;

/**
 * Configured price model
 */
class ConfiguredPrice extends CatalogPrice\FinalPrice
{
    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @var \Magento\Bundle\Pricing\BundleOptionService
     */
    protected $optionService;

    /**
     * @var null|ItemInterface
     */
    protected $item;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param AmountFactory $amountFactory
     * @param BundleOptionService $optionService
     * @param ItemInterface $item
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        AmountFactory $amountFactory,
        BundleOptionService $optionService,
        ItemInterface $item = null
    ) {
        $this->amountFactory = $amountFactory;
        $this->optionService = $optionService;
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
     * Get Options with attached Selections collection
     *
     * @return array|\Magento\Bundle\Model\Resource\Option\Collection
     */
    public function getOptions()
    {
        $bundleProduct = $this->product;
        $bundleOptions = [];
        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $bundleProduct->getTypeInstance();

        // get bundle options
        $optionsQuoteItemOption = $this->item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : array();
        if ($bundleOptionsIds) {
            /** @var \Magento\Bundle\Model\Resource\Option\Collection $optionsCollection */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $bundleProduct);
            // get and add bundle selections collection
            $selectionsQuoteItemOption = $this->item->getOptionByCode('bundle_selection_ids');
            $bundleSelectionIds = unserialize($selectionsQuoteItemOption->getValue());
            if ($bundleSelectionIds) {
                $selectionsCollection = $typeInstance->getSelectionsByIds($bundleSelectionIds, $bundleProduct);
                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            }
        }
        return $bundleOptions;
    }

    /**
     * Option amount calculation for bundle product
     *
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getOptionsAmount()
    {
        $amountList = [$this->calculator->getAmount($this->basePrice->getValue(), $this->product)];
        foreach ($this->getOptions() as $option) {
            $amountList = array_merge(
                $amountList,
                $this->optionService->createSelectionAmountList($this->product, $option, true)
            );
        }

        $fullAmount = 0.;
        $adjustments = [];
        /** @var \Magento\Pricing\Amount\AmountInterface $itemAmount */
        foreach ($amountList as $itemAmount) {
            $fullAmount += $itemAmount->getValue();
            foreach ($itemAmount->getAdjustmentAmounts() as $code => $adjustment) {
                $adjustments[$code] = isset($adjustments[$code]) ? $adjustments[$code] + $adjustment : $adjustment;
            }
        }
        return $this->amountFactory->create($fullAmount, $adjustments);
    }

    /**
     * Get price value
     *
     * @return float
     */
    public function getValue()
    {
        $bundleOptionsPrice = $this->priceInfo->getPrice(BundleOptionPrice::PRICE_CODE, $this->quantity);
        return parent::getValue() + $this->basePrice->calculateBaseValue($bundleOptionsPrice->getValue());
    }

    /**
     * Get Amount for configured price which is included amount for all selected options
     *
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getAmount()
    {
        return $this->item ? $this->getOptionsAmount() : parent::getAmount();
    }
}
