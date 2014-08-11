<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Adminhtml\Items\Price;

use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Block\Item\Price\Renderer as ItemPriceRenderer;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Quote\Item\AbstractItem as QuoteItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;

/**
 * Sales Order items price column renderer
 */
class Renderer extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Tax\Block\Item\Price\Renderer
     */
    protected $itemPriceRenderer;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param TaxHelper $taxHelper
     * @param ItemPriceRenderer $itemPriceRenderer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        TaxHelper $taxHelper,
        ItemPriceRenderer $itemPriceRenderer,
        array $data = array()
    ) {
        $this->_optionFactory = $optionFactory;
        $this->itemPriceRenderer = $itemPriceRenderer;
        $this->itemPriceRenderer->setZone('sales');
        parent::__construct($context, $stockItemService, $registry, $optionFactory, $data);
    }

    public function setItem($item)
    {
        $this->itemPriceRenderer->setItem($item);
        parent::setItem($item);
    }

    /**
     * Return whether display setting is to display price including tax
     *
     * @return bool
     */
    public function displayPriceInclTax()
    {
        return $this->itemPriceRenderer->displayPriceInclTax();
    }

    /**
     * Return whether display setting is to display price excluding tax
     *
     * @return bool
     */
    public function displayPriceExclTax()
    {
        return $this->itemPriceRenderer->displayPriceExclTax();
    }

    /**
     * Return whether display setting is to display both price including tax and price excluding tax
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->itemPriceRenderer->displayBothPrices();
    }

    /**
     * Calculate total amount for the item
     *
     * @param Item|QuoteItem|InvoiceItem|CreditmemoItem $item
     * @return mixed
     */
    public function getTotalAmount($item)
    {
        $totalAmount = $item->getRowTotal()
            + $item->getTaxAmount()
            + $item->getHiddenTaxAmount()
            - $item->getDiscountAmount();

        return $totalAmount;
    }

    /**
     * Calculate base total amount for the item
     *
     * @param Item|QuoteItem|InvoiceItem|CreditmemoItem $item
     * @return mixed
     */
    public function getBaseTotalAmount($item)
    {
        $baseTotalAmount =  $item->getBaseRowTotal()
            + $item->getBaseTaxAmount()
            + $item->getBaseHiddenTaxAmount()
            - $item->getBaseDiscountAmount();

        return $baseTotalAmount;
    }

    /**
     * Retrieve formated price, use different formatter depending on type of item
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        $item = $this->getItem();
        if ($item instanceof QuoteItem) {
            return $item->getStore()->formatPrice($price);
        } else {
            return $this->getOrder()->formatPrice($price);
        }
    }
}
