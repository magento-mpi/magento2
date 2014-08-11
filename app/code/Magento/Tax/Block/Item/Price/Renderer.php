<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Item\Price;

use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Object as MagentoObject;
use Magento\Sales\Model\Quote\Item\AbstractItem as QuoteItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\CreditMemo\Item as CreditMemoItem;
use Magento\Framework\Pricing\Render as PricingRender;
use Magento\Checkout\Helper\Data as CheckoutHelper;

/**
 * Item price render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var QuoteItem|OrderItem|InvoiceItem|CreditMemoItem
     */
    protected $item;

    /**
     * @var string|int|null
     */
    protected $storeId = null;

    /**
     * Set the display area, e.g., cart, sales, etc.
     *
     * @var string
     */
    protected $zone = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param TaxHelper $taxHelper
     * @param CheckoutHelper $checkoutHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        TaxHelper $taxHelper,
        CheckoutHelper $checkoutHelper,
        array $data = array()
    ) {
        $this->taxHelper = $taxHelper;
        $this->checkoutHelper = $checkoutHelper;
        if (isset($data['zone'])) {
            $this->zone = $data['zone'];
        }
        parent::__construct($context, $data);
    }

    /**
     * Set item for render
     *
     * @param QuoteItem|OrderItem|InvoiceItem|CreditMemoItem $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        $this->storeId = $item->getStoreId();
        return $this;
    }

    /**
     * Get display zone
     *
     * @return string|null
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set display zone
     *
     * @param string $zone
     * @return $this
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * @return int|null|string
     */
    public function getStoreId()
    {
        return $this->storeId;
    }
    /**
     * Get quote or order item
     *
     * @return CreditMemoItem|InvoiceItem|OrderItem|QuoteItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Return whether display setting is to display price including tax
     *
     * @return bool
     */
    public function displayPriceInclTax()
    {
        switch ($this->zone) {
            case PricingRender::ZONE_CART:
                return $this->taxHelper->displayCartPriceInclTax($this->storeId);
            case PricingRender::ZONE_EMAIL:
            case PricingRender::ZONE_SALES:
                return $this->taxHelper->displaySalesPriceInclTax($this->storeId);
            default:
                return $this->taxHelper->displayCartPriceInclTax($this->storeId);
        }
    }

    /**
     * Return whether display setting is to display price excluding tax
     *
     * @return bool
     */
    public function displayPriceExclTax()
    {
        switch ($this->zone) {
            case PricingRender::ZONE_CART:
                return $this->taxHelper->displayCartPriceExclTax($this->storeId);
            case PricingRender::ZONE_EMAIL:
            case PricingRender::ZONE_SALES:
                return $this->taxHelper->displaySalesPriceExclTax($this->storeId);
            default:
                return $this->taxHelper->displayCartPriceExclTax($this->storeId);
        }
    }

    /**
     * Return whether display setting is to display both price including tax and price excluding tax
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        switch ($this->zone) {
            case PricingRender::ZONE_CART:
                return $this->taxHelper->displayCartBothPrices($this->storeId);
            case PricingRender::ZONE_EMAIL:
            case PricingRender::ZONE_SALES:
                return $this->taxHelper->displaySalesBothPrices($this->storeId);
            default:
                return $this->taxHelper->displayCartBothPrices($this->storeId);
        }
        return $this->taxHelper->displayCartBothPrices($this->storeId);
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->checkoutHelper->formatPrice($price);
    }

    /**
     * Get item price in display currency or order currency depending
     * on item type
     *
     * @return float
     */
    public function getItemDisplayPriceExclTax()
    {
        $item = $this->getItem();
        if ($item instanceof QuoteItem) {
            return $item->getCalculationPrice();
        } else {
            return $item->getPrice();
        }
    }
}
