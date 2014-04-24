<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Price;

use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Bundle\Pricing\BundleOptionService;

/**
 * Bundle option price model
 */
class BundleOptionPrice extends AbstractPrice implements BundleOptionPriceInterface
{
    /**
     * Price model code
     */
    const PRICE_CODE = 'bundle_option';

    /**
     * @var BundleCalculatorInterface
     */
    protected $calculator;

    /**
     * @var \Magento\Bundle\Pricing\BundleOptionService
     */
    protected $optionService;

    /**
     * @var float|bool|null
     */
    protected $maximalPrice;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param BundleCalculatorInterface $calculator
     * @param BundleOptionService $optionService
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        BundleCalculatorInterface $calculator,
        BundleOptionService $optionService
    ) {
        $this->optionService = $optionService;
        parent::__construct($saleableItem, $quantity, $calculator);
        $this->product->setQty($this->quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (null === $this->value) {
            $this->value = $this->calculateOptions();
        }
        return $this->value;
    }

    /**
     * Getter for maximal price of options
     *
     * @return bool|float
     */
    public function getMaxValue()
    {
        if (null === $this->maximalPrice) {
            $this->maximalPrice = $this->calculateOptions(false);
        }
        return $this->maximalPrice;
    }

    /**
     * Get Options with attached Selections collection
     *
     * @return \Magento\Bundle\Model\Resource\Option\Collection
     */
    public function getOptions()
    {
        $bundleProduct = $this->product;
        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $bundleProduct->getTypeInstance();
        $typeInstance->setStoreFilter($bundleProduct->getStoreId(), $bundleProduct);

        /** @var \Magento\Bundle\Model\Resource\Option\Collection $optionCollection */
        $optionCollection = $typeInstance->getOptionsCollection($bundleProduct);

        $selectionCollection = $typeInstance->getSelectionsCollection(
            $typeInstance->getOptionsIds($bundleProduct),
            $bundleProduct
        );

        $priceOptions = $optionCollection->appendSelections($selectionCollection, false, false);
        return $priceOptions;
    }

    /**
     * @param \Magento\Bundle\Model\Selection $selection
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getOptionSelectionAmount($selection)
    {
        return $this->optionService->createSelectionAmount($selection, $this->product);
    }

    /**
     * @param bool $searchMin
     * @return bool|float
     */
    protected function calculateOptions($searchMin = true)
    {
        $price = false;
        $amountList = [];
        /* @var $option \Magento\Bundle\Model\Option */
        foreach ($this->getOptions() as $option) {
            if (!$option->getSelections()) {
                continue;
            }
            if ($option->getSelections() && !($searchMin && !$option->getRequired())) {
                $selectionAmountList = $this->optionService->createSelectionAmountList($this->product, $option, true);
                $amountList = array_merge(
                    $amountList,
                    $this->optionService->processOptions($option, $selectionAmountList, $searchMin)
                );
            }
        }
        if (!empty($amountList)) {
            $price = 0.;
            foreach ($amountList as $itemAmount) {
                $price += $itemAmount->getValue();
            }
        }
        return $price;
    }

    /**
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getAmount()
    {
        return $this->calculator->getOptionsAmount($this->product);
    }
}
