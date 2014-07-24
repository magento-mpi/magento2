<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Block\Item\Price\Renderer;

use Magento\Sales\Model\Quote\Item\AbstractItem;
use Magento\Weee\Model\Tax;

/**
 * Item unit price render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Unit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Item
     */
    protected $_item;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Weee\Helper\Data $weeeHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Weee\Helper\Data $weeeHelper,
        array $data = array()
    ) {
        $this->taxHelper = $taxHelper;
        $this->weeeHelper = $weeeHelper;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Set item for render
     *
     * @param AbstractItem $item
     * @return $this
     */
    public function setItem(AbstractItem $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get quote item
     *
     * @return AbstractItem
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Whether need to display price including tax
     *
     * @return bool
     */
    public function displayPriceInclTax()
    {
        return $this->taxHelper->displayCartPriceInclTax() || $this->taxHelper->displayCartBothPrices();
    }

    /**
     * Whether need to display price excluding tax
     *
     * @return bool
     */
    public function displayPriceExclTax()
    {
        return $this->taxHelper->displayCartPriceExclTax() || $this->taxHelper->displayCartBothPrices();
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

        if (!$this->weeeHelper->typeOfDisplay([Tax::DISPLAY_INCL_DESCR], 'sales')) {
            return false;
        }

        if (!$this->_item->getWeeeTaxAppliedAmount()) {
            return false;
        }

        return true;
    }

    public function getDisplayPriceInclTax()
    {
        $priceInclTax = $this->_item->getPriceInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $priceInclTax;
        }

        if ($this->weeeHelper->typeOfDisplay([Tax::DISPLAY_INCL_DESCR, Tax::DISPLAY_INCL], 'sales')) {
            return $priceInclTax + $this->weeeHelper->getWeeeTaxInclTax($this->_item);
        }

        return $priceInclTax;
    }

    public function getDisplayPriceExclTax()
    {
        $priceExclTax = $this->_item->getCalculationPrice();

        if (!$this->weeeHelper->isEnabled()) {
            return $priceExclTax;
        }

        if ($this->weeeHelper->typeOfDisplay([Tax::DISPLAY_INCL_DESCR, Tax::DISPLAY_INCL], 'sales')) {
            return $priceExclTax + $this->_item->getWeeeTaxAppliedAmount();
        }

        return $priceExclTax;
    }

    public function getFinalDisplayPriceInclTax()
    {
        $priceInclTax = $this->_item->getPriceInclTax();

        if (!$this->weeeHelper->isEnabled()) {
            return $priceInclTax;
        }

        return $priceInclTax + $this->weeeHelper->getWeeeTaxInclTax($this->_item);
    }

    public function getFinalDisplayPriceExclTax()
    {
        $priceExclTax = $this->_item->getCalculationPrice();

        if (!$this->weeeHelper->isEnabled()) {
            return $priceExclTax;
        }

        return $priceExclTax + $this->_item->getWeeeTaxAppliedAmount();
    }

    public function displayFinalPrice()
    {
        if (!$this->weeeHelper->typeOfDisplay(Tax::DISPLAY_EXCL_DESCR_INCL, 'sales')) {
            return false;
        }

        if (!$this->_item->getWeeeTaxAppliedAmount()) {
            return false;
        }
        return true;
    }

}
