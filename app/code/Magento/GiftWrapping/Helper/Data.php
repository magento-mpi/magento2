<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Helper;

use Magento\GiftWrapping\Model\System\Config\Source\Display\Type as DisplayType;
use Magento\Tax\Service\V1\Data\QuoteDetailsBuilder;
use Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder as QuoteDetailsItemBuilder;
use Magento\Tax\Service\V1\TaxCalculationServiceInterface;
use Magento\Customer\Model\Address\Converter as AddressConverter;

/**
 * Gift wrapping default helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Gift wrapping allow section in configuration
     */
    const XML_PATH_ALLOWED_FOR_ITEMS = 'sales/gift_options/wrapping_allow_items';

    const XML_PATH_ALLOWED_FOR_ORDER = 'sales/gift_options/wrapping_allow_order';

    /**
     * Gift wrapping tax class
     */
    const XML_PATH_TAX_CLASS = 'tax/classes/wrapping_tax_class';

    /**
     * Shopping cart display settings
     */
    const XML_PATH_PRICE_DISPLAY_CART_WRAPPING = 'tax/cart_display/gift_wrapping';

    const XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD = 'tax/cart_display/printed_card';

    /**
     * Sales display settings
     */
    const XML_PATH_PRICE_DISPLAY_SALES_WRAPPING = 'tax/sales_display/gift_wrapping';

    const XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD = 'tax/sales_display/printed_card';

    /**
     * Gift receipt and printed card settings
     */
    const XML_PATH_ALLOW_GIFT_RECEIPT = 'sales/gift_options/allow_gift_receipt';

    const XML_PATH_ALLOW_PRINTED_CARD = 'sales/gift_options/allow_printed_card';

    const XML_PATH_PRINTED_CARD_PRICE = 'sales/gift_options/printed_card_price';

    /**
     * Constant for gift wrapping taxable item code
     */
    const TAXABLE_ITEM_CODE = 'giftwrapping_code';

    /**
     * Constant for type of taxable item
     */
    const TAXABLE_ITEM_TYPE = 'giftwrapping_type';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Tax\Service\V1\Data\QuoteDetailsBuilder
     */
    protected $quoteDetailsBuilder;

    /**
     * @var \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder
     */
    protected $quoteDetailsItemBuilder;

    /**
     * @var \Magento\Tax\Service\V1\TaxCalculationServiceInterface
     */
    protected $taxCalculationService;

    /**
     * @var \Magento\Customer\Model\Address\Converter
     */
    protected $addressConverter;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param QuoteDetailsBuilder $quoteDetailsBuilder
     * @param QuoteDetailsItemBuilder $quoteDetailsItemBuilder
     * @param TaxCalculationServiceInterface $taxCalculationService
     * @param AddressConverter $addressConverter
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        QuoteDetailsBuilder $quoteDetailsBuilder,
        QuoteDetailsItemBuilder $quoteDetailsItemBuilder,
        TaxCalculationServiceInterface $taxCalculationService,
        AddressConverter $addressConverter
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->quoteDetailsBuilder = $quoteDetailsBuilder;
        $this->quoteDetailsItemBuilder = $quoteDetailsItemBuilder;
        $this->taxCalculationService = $taxCalculationService;
        $this->addressConverter = $addressConverter;
        parent::__construct($context);
    }

    /**
     * Check availablity of gift wrapping for product
     *
     * @param int $productConfig
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function isGiftWrappingAvailableForProduct($productConfig, $store = null)
    {
        if (is_null($productConfig) || '' === $productConfig) {
            return $this->isGiftWrappingAvailableForItems($store);
        } else {
            return $productConfig;
        }
    }

    /**
     * Check availablity of gift wrapping on items level
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return string|null
     */
    public function isGiftWrappingAvailableForItems($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_ALLOWED_FOR_ITEMS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check availablity of gift wrapping on order level
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return string|null
     */
    public function isGiftWrappingAvailableForOrder($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_ALLOWED_FOR_ORDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check ability to display both prices for printed card
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return string|null
     */
    public function getWrappingTaxClass($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_TAX_CLASS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check printed card allow
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return string|null
     */
    public function allowPrintedCard($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_ALLOW_PRINTED_CARD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check allow gift receipt
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return string|null
     */
    public function allowGiftReceipt($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_ALLOW_GIFT_RECEIPT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return printed card base price
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return string|null
     */
    public function getPrintedCardPrice($store = null)
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_PRINTED_CARD_PRICE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check ability to display prices including tax for gift wrapping in shopping cart
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displayCartWrappingIncludeTaxPrice($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_BOTH ||
            $configValue == DisplayType::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check ability to display prices excluding tax for gift wrapping in shopping cart
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displayCartWrappingExcludeTaxPrice($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check ability to display both prices for gift wrapping in shopping cart
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displayCartWrappingBothPrices($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_CART_WRAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check ability to display prices including tax for printed card in shopping cart
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displayCartCardIncludeTaxPrice($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_BOTH ||
            $configValue == DisplayType::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check ability to display both prices for printed card in shopping cart
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displayCartCardBothPrices($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_CART_PRINTED_CARD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check ability to display prices including tax for gift wrapping in backend sales
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displaySalesWrappingIncludeTaxPrice($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_BOTH ||
            $configValue == DisplayType::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check ability to display prices excluding tax for gift wrapping in backend sales
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displaySalesWrappingExcludeTaxPrice($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check ability to display both prices for gift wrapping in backend sales
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displaySalesWrappingBothPrices($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_SALES_WRAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_BOTH;
    }

    /**
     * Check ability to display prices including tax for printed card in backend sales
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displaySalesCardIncludeTaxPrice($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_BOTH ||
            $configValue == DisplayType::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check ability to display both prices for printed card in backend sales
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function displaySalesCardBothPrices($store = null)
    {
        $configValue = $this->_scopeConfig->getValue(
            self::XML_PATH_PRICE_DISPLAY_SALES_PRINTED_CARD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue == DisplayType::DISPLAY_TYPE_BOTH;
    }

    /**
     * Return totals of data object
     *
     * @param  \Magento\Framework\Object $dataObject
     * @return array
     */
    public function getTotals($dataObject)
    {
        $totals = array();

        $displayWrappingBothPrices = false;
        $displayWrappingIncludeTaxPrice = false;
        $displayCardBothPrices = false;
        $displayCardIncludeTaxPrice = false;

        if ($dataObject instanceof \Magento\Sales\Model\Order ||
            $dataObject instanceof \Magento\Sales\Model\Order\Invoice ||
            $dataObject instanceof \Magento\Sales\Model\Order\Creditmemo
        ) {
            $displayWrappingBothPrices = $this->displaySalesWrappingBothPrices();
            $displayWrappingIncludeTaxPrice = $this->displaySalesWrappingIncludeTaxPrice();
            $displayCardBothPrices = $this->displaySalesCardBothPrices();
            $displayCardIncludeTaxPrice = $this->displaySalesCardIncludeTaxPrice();
        } elseif ($dataObject instanceof \Magento\Sales\Model\Quote\Address\Total) {
            $displayWrappingBothPrices = $this->displayCartWrappingBothPrices();
            $displayWrappingIncludeTaxPrice = $this->displayCartWrappingIncludeTaxPrice();
            $displayCardBothPrices = $this->displayCartCardBothPrices();
            $displayCardIncludeTaxPrice = $this->displayCartCardIncludeTaxPrice();
        }

        /**
         * Gift wrapping for order totals
         */
        if ($displayWrappingBothPrices || $displayWrappingIncludeTaxPrice) {
            if ($displayWrappingBothPrices) {
                $this->_addTotalToTotals(
                    $totals,
                    'gw_order_excl',
                    $dataObject->getGwPrice(),
                    $dataObject->getGwBasePrice(),
                    __('Gift Wrapping for Order (Excl. Tax)')
                );
            }
            $this->_addTotalToTotals(
                $totals,
                'gw_order_incl',
                $dataObject->getGwPrice() + $dataObject->getGwTaxAmount(),
                $dataObject->getGwBasePrice() + $dataObject->getGwBaseTaxAmount(),
                __('Gift Wrapping for Order (Incl. Tax)')
            );
        } else {
            $this->_addTotalToTotals(
                $totals,
                'gw_order',
                $dataObject->getGwPrice(),
                $dataObject->getGwBasePrice(),
                __('Gift Wrapping for Order')
            );
        }

        /**
         * Gift wrapping for items totals
         */
        if ($displayWrappingBothPrices || $displayWrappingIncludeTaxPrice) {
            $this->_addTotalToTotals(
                $totals,
                'gw_items_incl',
                $dataObject->getGwItemsPrice() + $dataObject->getGwItemsTaxAmount(),
                $dataObject->getGwItemsBasePrice() + $dataObject->getGwItemsBaseTaxAmount(),
                __('Gift Wrapping for Items (Incl. Tax)')
            );
            if ($displayWrappingBothPrices) {
                $this->_addTotalToTotals(
                    $totals,
                    'gw_items_excl',
                    $dataObject->getGwItemsPrice(),
                    $dataObject->getGwItemsBasePrice(),
                    __('Gift Wrapping for Items (Excl. Tax)')
                );
            }
        } else {
            $this->_addTotalToTotals(
                $totals,
                'gw_items',
                $dataObject->getGwItemsPrice(),
                $dataObject->getGwItemsBasePrice(),
                __('Gift Wrapping for Items')
            );
        }

        /**
         * Printed card totals
         */
        if ($displayCardBothPrices || $displayCardIncludeTaxPrice) {
            $this->_addTotalToTotals(
                $totals,
                'gw_printed_card_incl',
                $dataObject->getGwCardPrice() + $dataObject->getGwCardTaxAmount(),
                $dataObject->getGwCardBasePrice() + $dataObject->getGwCardBaseTaxAmount(),
                __('Printed Card (Incl. Tax)')
            );
            if ($displayCardBothPrices) {
                $this->_addTotalToTotals(
                    $totals,
                    'gw_printed_card_excl',
                    $dataObject->getGwCardPrice(),
                    $dataObject->getGwCardBasePrice(),
                    __('Printed Card (Excl. Tax)')
                );
            }
        } else {
            $this->_addTotalToTotals(
                $totals,
                'gw_printed_card',
                $dataObject->getGwCardPrice(),
                $dataObject->getGwCardBasePrice(),
                __('Printed Card')
            );
        }

        return $totals;
    }

    /**
     * Add total into array totals
     *
     * @param  array &$totals
     * @param  string $code
     * @param  float $value
     * @param  float $baseValue
     * @param  string $label
     * @return void
     */
    protected function _addTotalToTotals(&$totals, $code, $value, $baseValue, $label)
    {
        if ($value == 0 && $baseValue == 0) {
            return;
        }
        $total = array('code' => $code, 'value' => $value, 'base_value' => $baseValue, 'label' => $label);
        $totals[] = $total;
    }

    /**
     * Get gift wrapping items price with tax processing
     *
     * @param \Magento\Framework\Object $item
     * @param float $price
     * @param bool $includeTax
     * @param null|\Magento\Customer\Model\Address $shippingAddress
     * @param null|\Magento\Customer\Model\Address $billingAddress
     * @param null|int $ctc
     * @param mixed $store
     * @return float
     */
    public function getPrice(
        $item,
        $price,
        $includeTax = false,
        $shippingAddress = null,
        $billingAddress = null,
        $ctc = null,
        $store = null
    ) {
        if (!$price) {
            return $price;
        }
        $store = $this->_storeManager->getStore($store);
        $taxClassId = $item->getTaxClassId();
        if ($taxClassId && $includeTax) {
            $shippingAddressDataObject = null;
            if ($shippingAddress instanceof \Magento\Customer\Model\Address\AbstractAddress) {
                $shippingAddressDataObject = $this->addressConverter->createAddressFromModel(
                    $shippingAddress,
                    null,
                    null
                );
            }

            $billingAddressDataObject = null;
            if ($billingAddress instanceof \Magento\Customer\Model\Address\AbstractAddress) {
                $billingAddressDataObject = $this->addressConverter->createAddressFromModel(
                    $billingAddress,
                    null,
                    null
                );
            }

            $taxableItem = $this->quoteDetailsItemBuilder->setQuantity(1)
                ->setCode(self::TAXABLE_ITEM_CODE)
                ->setTaxClassId($taxClassId)
                ->setTaxIncluded(false)
                ->setType(self::TAXABLE_ITEM_TYPE)
                ->setUnitPrice($price)
                ->create();
            $quoteDetails = $this->quoteDetailsBuilder
                ->setShippingAddress($shippingAddressDataObject)
                ->setBillingAddress($billingAddressDataObject)
                ->setCustomerTaxClassId($ctc)
                ->setItems([$taxableItem])
                ->create();

            $storeId = null;
            if ($store) {
                $storeId = $store->getId();
            }
            $taxDetails = $this->taxCalculationService->calculateTax($quoteDetails, $storeId);
            $taxDetailsItems = $taxDetails->getItems();
            $taxDetailsItem = array_pop($taxDetailsItems);
            return $taxDetailsItem->getPriceInclTax();
        }
        return $store->roundPrice($price);
    }
}
