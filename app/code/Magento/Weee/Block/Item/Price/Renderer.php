<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Block\Item\Price;

use Magento\Weee\Model\Tax as WeeeDisplayConfig;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Item price render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Tax\Block\Item\Price\Renderer
{
    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Weee\Helper\Data $weeeHelper
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Weee\Helper\Data $weeeHelper,
        PriceCurrencyInterface $priceCurrency,
        array $data = array()
    ) {
        $this->weeeHelper = $weeeHelper;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $taxHelper, $checkoutHelper, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Whether to display weee details together with price
     *
     * @return bool
     */
    public function displayPriceWithWeeeDetails()
    {
        if (!$this->weeeHelper->isEnabled()) {
            return false;
        }

        $displayWeeeDetails = $this->weeeHelper->typeOfDisplay(
            [WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_EXCL_DESCR_INCL],
            $this->getZone(),
            $this->getStoreId()
        );
        if (!$displayWeeeDetails) {
            return false;
        }

        if (!$this->getItem()->getWeeeTaxAppliedAmount()) {
            return false;
        }

        return true;
    }

    /**
     * Return the flag whether to include weee in the price
     *
     * @return bool|int
     */
    public function getIncludeWeeeFlag()
    {
        $includeWeee = $this->weeeHelper->typeOfDisplay(
            [WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL],
            $this->getZone(),
            $this->getStoreId()
        );
        return $includeWeee;
    }

    /**
     * Get display price for unit price including tax. The Weee amount will be added to unit price including tax
     * depending on Weee display setting
     *
     * @return float
     */
    public function getUnitDisplayPriceInclTax()
    {
        $priceInclTax = $this->getItem()->getPriceInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $priceInclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $priceInclTax + $this->weeeHelper->getWeeeTaxInclTax($this->getItem());
        }

        return $priceInclTax;
    }

    /**
     * Get base price for unit price including tax. The Weee amount will be added to unit price including tax
     * depending on Weee display setting
     *
     * @return float
     */
    public function getBaseUnitDisplayPriceInclTax()
    {
        $basePriceInclTax = $this->getItem()->getBasePriceInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $basePriceInclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $basePriceInclTax + $this->weeeHelper->getBaseWeeeTaxInclTax($this->getItem());
        }

        return $basePriceInclTax;
    }

    /**
     * Get display price for row total including tax. The Weee amount will be added to row total including tax
     * depending on Weee display setting
     *
     * @return float
     */
    public function getRowDisplayPriceInclTax()
    {
        $rowTotalInclTax = $this->getItem()->getRowTotalInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $rowTotalInclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $rowTotalInclTax + $this->weeeHelper->getRowWeeeTaxInclTax($this->getItem());
        }

        return $rowTotalInclTax;
    }

    /**
     * Get base price for row total including tax. The Weee amount will be added to row total including tax
     * depending on Weee display setting
     *
     * @return float
     */
    public function getBaseRowDisplayPriceInclTax()
    {
        $baseRowTotalInclTax = $this->getItem()->getBaseRowTotalInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $baseRowTotalInclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $baseRowTotalInclTax + $this->weeeHelper->getBaseRowWeeeTaxInclTax($this->getItem());
        }

        return $baseRowTotalInclTax;
    }

    /**
     * Get display price for unit price excluding tax. The Weee amount will be added to unit price
     * depending on Weee display setting
     *
     * @return float
     */
    public function getUnitDisplayPriceExclTax()
    {
        $priceExclTax = $this->getItemDisplayPriceExclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $priceExclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $priceExclTax + $this->getItem()->getWeeeTaxAppliedAmount();
        }

        return $priceExclTax;
    }

    /**
     * Get base price for unit price excluding tax. The Weee amount will be added to unit price
     * depending on Weee display setting
     *
     * @return float
     */
    public function getBaseUnitDisplayPriceExclTax()
    {
        $basePriceExclTax = $this->getItem()->getBasePrice();

        if (!$this->weeeHelper->isEnabled()) {
            return $basePriceExclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $basePriceExclTax + $this->getItem()->getBaseWeeeTaxAppliedAmount();
        }

        return $basePriceExclTax;
    }

    /**
     * Get display price for row total excluding tax. The Weee amount will be added to row total
     * depending on Weee display setting
     *
     * @return float
     */
    public function getRowDisplayPriceExclTax()
    {
        $rowTotalExclTax = $this->getItem()->getRowTotal();

        if (!$this->weeeHelper->isEnabled()) {
            return $rowTotalExclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $rowTotalExclTax + $this->getItem()->getWeeeTaxAppliedRowAmount();
        }

        return $rowTotalExclTax;
    }

    /**
     * Get base price for row total excluding tax. The Weee amount will be added to row total
     * depending on Weee display setting
     *
     * @return float
     */
    public function getBaseRowDisplayPriceExclTax()
    {
        $baseRowTotalExclTax = $this->getItem()->getBaseRowTotal();

        if (!$this->weeeHelper->isEnabled()) {
            return $baseRowTotalExclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $baseRowTotalExclTax + $this->getItem()->getBaseWeeeTaxAppliedRowAmount();
        }

        return $baseRowTotalExclTax;
    }

    /**
     * Get final unit display price including tax, this will add Weee amount to unit price include tax
     *
     * @return float
     */
    public function getFinalUnitDisplayPriceInclTax()
    {
        $priceInclTax = $this->getItem()->getPriceInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $priceInclTax;
        }

        return $priceInclTax + $this->weeeHelper->getWeeeTaxInclTax($this->getItem());
    }

    /**
     * Get base final unit display price including tax, this will add Weee amount to unit price include tax
     *
     * @return float
     */
    public function getBaseFinalUnitDisplayPriceInclTax()
    {
        $basePriceInclTax = $this->getItem()->getBasePriceInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $basePriceInclTax;
        }

        return $basePriceInclTax + $this->weeeHelper->getBaseWeeeTaxInclTax($this->getItem());
    }

    /**
     * Get final row display price including tax, this will add weee amount to rowTotalInclTax
     *
     * @return float
     */
    public function getFinalRowDisplayPriceInclTax()
    {
        $rowTotalInclTax = $this->getItem()->getRowTotalInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $rowTotalInclTax;
        }

        return $rowTotalInclTax + $this->weeeHelper->getRowWeeeTaxInclTax($this->getItem());
    }

    /**
     * Get base final row display price including tax, this will add weee amount to rowTotalInclTax
     *
     * @return float
     */
    public function getBaseFinalRowDisplayPriceInclTax()
    {
        $baseRowTotalInclTax = $this->getItem()->getBaseRowTotalInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $baseRowTotalInclTax;
        }

        return $baseRowTotalInclTax + $this->weeeHelper->getBaseRowWeeeTaxInclTax($this->getItem());
    }

    /**
     * Get final unit display price excluding tax
     *
     * @return float
     */
    public function getFinalUnitDisplayPriceExclTax()
    {
        $priceExclTax = $this->getItemDisplayPriceExclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $priceExclTax;
        }

        return $priceExclTax + $this->getItem()->getWeeeTaxAppliedAmount();
    }

    /**
     * Get base final unit display price excluding tax
     *
     * @return float
     */
    public function getBaseFinalUnitDisplayPriceExclTax()
    {
        $basePriceExclTax = $this->getItem()->getBasePrice();

        if (!$this->weeeHelper->isEnabled()) {
            return $basePriceExclTax;
        }

        return $basePriceExclTax + $this->getItem()->getBaseWeeeTaxAppliedAmount();
    }

    /**
     * Get final row display price excluding tax, this will add Weee amount to rowTotal
     *
     * @return float
     */
    public function getFinalRowDisplayPriceExclTax()
    {
        $rowTotalExclTax = $this->getItem()->getRowTotal();

        if (!$this->weeeHelper->isEnabled()) {
            return $rowTotalExclTax;
        }

        return $rowTotalExclTax + $this->getItem()->getWeeeTaxAppliedRowAmount();
    }

    /**
     * Get base final row display price excluding tax, this will add Weee amount to rowTotal
     *
     * @return float
     */
    public function getBaseFinalRowDisplayPriceExclTax()
    {
        $baseRowTotalExclTax = $this->getItem()->getBaseRowTotal();

        if (!$this->weeeHelper->isEnabled()) {
            return $baseRowTotalExclTax;
        }

        return $baseRowTotalExclTax + $this->getItem()->getBaseWeeeTaxAppliedRowAmount();
    }

    /**
     * Whether to display final price that include Weee amounts
     *
     * @return bool
     */
    public function displayFinalPrice()
    {
        $flag = $this->weeeHelper->typeOfDisplay(
            WeeeDisplayConfig::DISPLAY_EXCL_DESCR_INCL,
            $this->getZone(),
            $this->getStoreId()
        );

        if (!$flag) {
            return false;
        }

        if (!$this->getItem()->getWeeeTaxAppliedAmount()) {
            return false;
        }
        return true;
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->priceCurrency->format($price);
    }
}
