<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Pricing\Price;

use Magento\Catalog\Pricing\Price\AbstractPrice;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Bundle\Pricing\Adjustment\BundleCalculatorInterface;
use \Magento\Catalog\Model\Product;

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

    /**
     * @param Product $product
     * @param float $quantity
     * @param BundleCalculatorInterface $calculator
     * @param BundleSelectionFactory $bundleSelectionFactory
     */
    public function __construct(
        Product $product,
        $quantity,
        BundleCalculatorInterface $calculator,
        BundleSelectionFactory $bundleSelectionFactory
    ) {
        $this->selectionFactory = $bundleSelectionFactory;
        parent::__construct($product, $quantity, $calculator);
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
        if (null !== $this->priceOptions) {
            return $this->priceOptions;
        }
        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $this->product->getTypeInstance();
        $typeInstance->setStoreFilter($this->product->getStoreId(), $this->product);

        /** @var \Magento\Bundle\Model\Resource\Option\Collection $optionCollection */
        $optionCollection = $typeInstance->getOptionsCollection($this->product);

        $selectionCollection = $typeInstance->getSelectionsCollection(
            $typeInstance->getOptionsIds($this->product),
            $this->product
        );

        $this->priceOptions = $optionCollection->appendSelections($selectionCollection, false, false);
        return $this->priceOptions;
    }

    /**
     * @param \Magento\Bundle\Model\Selection $selection
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getOptionSelectionAmount($selection)
    {
        return $this->createSelection($selection)->getAmount();
    }

    /**
     * @param \Magento\Bundle\Model\Selection $selection
     * @return \Magento\Bundle\Pricing\Price\BundleSelectionPrice
     */
    protected function createSelection($selection)
    {
        return $this->selectionFactory->create($this->product, $selection, $selection->getSelectionQty());
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
            $amountList = array_merge($amountList, $this->processOptions($option, $searchMin));
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
     * @param \Magento\Bundle\Model\Option $option
     * @param bool $searchMin
     * @return \Magento\Pricing\Amount\AmountInterface[]
     */
    protected function processOptions($option, $searchMin = true)
    {
        $result = [];
        foreach ($option->getSelections() as $selection) {
            /* @var $selection \Magento\Bundle\Model\Selection */
            if (!$selection->isSalable() || ($searchMin && !$option->getRequired())) {
                // @todo CatalogInventory Show out of stock Products
                continue;
            }
            $current = $this->createSelection($selection);
            if (empty($result)) {
                $result = [$current];
                continue;
            }
            if ($searchMin && end($result)->getValue() > $current->getValue()) {
                $result = [$current];
            } elseif (!$searchMin && $option->isMultiSelection()) {
                $result[] = $current;
            } elseif (!$searchMin && !$option->isMultiSelection() && end($result)->getValue() < $current->getValue()) {
                $result = [$current];
            }
        }
        return $result;
    }

    /**
     * @return \Magento\Pricing\Amount\AmountInterface
     */
    public function getAmount()
    {
        return $this->calculator->getOptionsAmount($this->product);
    }
}
