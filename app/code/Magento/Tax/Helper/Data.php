<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog data helper
 */
class Magento_Tax_Helper_Data extends Magento_Core_Helper_Abstract
{
    const PRICE_CONVERSION_PLUS = 1;
    const PRICE_CONVERSION_MINUS = 2;

    const CONFIG_DEFAULT_CUSTOMER_TAX_CLASS = 'tax/classes/default_customer_tax_class';
    const CONFIG_DEFAULT_PRODUCT_TAX_CLASS = 'tax/classes/default_product_tax_class';

    /**
     * Tax configuration object
     *
     * @var Magento_Tax_Model_Config
     */
    protected $_config      = null;
    protected $_calculator  = null;
    protected $_displayTaxColumn;
    protected $_taxData;
    protected $_priceIncludesTax;
    protected $_shippingPriceIncludesTax;
    protected $_applyTaxAfterDiscount;
    protected $_priceDisplayType;
    protected $_shippingPriceDisplayType;

    /**
     * Postcode cut to this length when creating search templates
     *
     * @var integer
     */
    protected $_postCodeSubStringLength = 10;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;
    
    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Tax_Model_Config $taxConfig
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Tax_Model_Config $taxConfig
    ) {
        parent::__construct($context);
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_config = $taxConfig;
        $this->_coreData = $coreData;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Return max postcode length to create search templates
     *
     * @return integer  $len
     */
    public function getPostCodeSubStringLength()
    {
        $len = (int)$this->_postCodeSubStringLength;
        if ($len <= 0) {
            $len = 10;
        }
        return $len;
    }

    /**
     * Get tax configuration object
     *
     * @return Magento_Tax_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Get tax calculation object
     *
     * @return  Magento_Tac_Model_Calculation
     */
    public function getCalculator()
    {
        if ($this->_calculator === null) {
            $this->_calculator = Mage::getSingleton('Magento_Tax_Model_Calculation');
        }
        return $this->_calculator;
    }

    /**
     * Get product price including store convertion rate
     *
     * @param   Magento_Catalog_Model_Product $product
     * @param   null|string $format
     * @return  float|string
     */
    public function getProductPrice($product, $format=null)
    {
        try {
            $value = $product->getPrice();
            $value = Mage::app()->getStore()->convertPrice($value, $format);
        } catch (Exception $e){
            $value = $e->getMessage();
        }
        return $value;
    }

    /**
     * Check if product prices inputed include tax
     *
     * @param   mix $store
     * @return  bool
     */
    public function priceIncludesTax($store=null)
    {
        return $this->_config->priceIncludesTax($store) || $this->_config->getNeedUseShippingExcludeTax();
    }

    /**
     * Check what taxes should be applied after discount
     *
     * @param   mixed $store
     * @return  bool
     */
    public function applyTaxAfterDiscount($store=null)
    {
        return $this->_config->applyTaxAfterDiscount($store);
    }

    /**
     * Output
     *
     * @param boolean $includes
     */
    public function getIncExcText($flag, $store=null)
    {
        if ($flag) {
            $s = __('Incl. Tax');
        } else {
            $s = __('Excl. Tax');
        }
        return $s;
    }

    /**
     * Get product price display type
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param   mixed $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return $this->_config->getPriceDisplayType($store);
    }

    /**
     * Check if necessary do product price conversion
     * If it necessary will be returned conversion type (minus or plus)
     *
     * @param   mixed $store
     * @return  false | int
     */
    public function needPriceConversion($store = null)
    {
        $res = false;
        if ($this->priceIncludesTax($store)) {
            switch ($this->getPriceDisplayType($store)) {
                case Magento_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                case Magento_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    return self::PRICE_CONVERSION_MINUS;
                case Magento_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                    $res = true;
            }
        } else {
            switch ($this->getPriceDisplayType($store)) {
                case Magento_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                case Magento_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    return self::PRICE_CONVERSION_PLUS;
                case Magento_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    $res = false;
            }
        }

        if ($res === false) {
            $res = $this->displayTaxColumn($store);
        }
        return $res;
    }

    /**
     * Check if need display full tax summary information in totals block
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayFullSummary($store = null)
    {
        return $this->_config->displayCartFullSummary($store);
    }

    /**
     * Check if need display zero tax in subtotal
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayZeroTax($store = null)
    {
        return $this->_config->displayCartZeroTax($store);
    }

    /**
     * Check if need display cart prices included tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartPriceInclTax($store = null)
    {
        return $this->_config->displayCartPricesInclTax($store);
    }

    /**
     * Check if need display cart prices excluding price
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartPriceExclTax($store = null)
    {
        return $this->_config->displayCartPricesExclTax($store);
    }

    /**
     * Check if need display cart prices excluding and including tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayCartBothPrices($store = null)
    {
        return $this->_config->displayCartPricesBoth($store);
    }

    /**
     * Check if need display order prices included tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesPriceInclTax($store = null)
    {
        return $this->_config->displaySalesPricesInclTax($store);
    }

    /**
     * Check if need display order prices excluding price
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesPriceExclTax($store = null)
    {
        return $this->_config->displaySalesPricesExclTax($store);
    }

    /**
     * Check if need display order prices excluding and including tax
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displaySalesBothPrices($store = null)
    {
        return $this->_config->displaySalesPricesBoth($store);
    }


    /**
     * Check if we need display price include and exclude tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalBoth($store = null)
    {
        return $this->_config->displaySalesSubtotalBoth($store);
    }

    /**
     * Check if we need display price include tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalInclTax($store = null)
    {
        return $this->_config->displaySalesSubtotalInclTax($store);
    }

    /**
     * Check if we need display price exclude tax for order/invoice subtotal
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalExclTax($store = null)
    {
        return $this->_config->displaySalesSubtotalExclTax($store);
    }

    /**
     * Check if need display tax column in for shopping cart/order items
     *
     * @param   mixed $store
     * @return  bool
     */
    public function displayTaxColumn($store = null)
    {
        return $this->_config->displayCartPricesBoth();
    }

    /**
     * Get prices javascript format json
     *
     * @param   mixed $store
     * @return  string
     */
    public function getPriceFormat($store = null)
    {
        Mage::app()->getLocale()->emulate($store);
        $priceFormat = Mage::app()->getLocale()->getJsPriceFormat();
        Mage::app()->getLocale()->revert();
        if ($store) {
            $priceFormat['pattern'] = Mage::app()->getStore($store)->getCurrentCurrency()->getOutputFormat();
        }
        return $this->_coreData->jsonEncode($priceFormat);
    }

    /**
     * Get all tax rates JSON for all product tax classes of specific store
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     * @return string
     */
    public function getAllRatesByProductClass($store=null)
    {
        return $this->_getAllRatesByProductClass($store);
    }


    /**
     * Get all tax rates JSON for all product tax classes of specific store
     *
     * array(
     *      value_{$productTaxVlassId} => $rate
     * )
     * @return string
     */
    protected function _getAllRatesByProductClass($store=null)
    {
        $result = array();
        $calc = Mage::getSingleton('Magento_Tax_Model_Calculation');
        $rates = $calc->getRatesForAllProductTaxClasses($calc->getRateOriginRequest($store));

        foreach ($rates as $class=>$rate) {
            $result["value_{$class}"] = $rate;
        }

        return $this->_coreData->jsonEncode($result);
    }

    /**
     * Get product price with all tax settings processing
     *
     * @param   Magento_Catalog_Model_Product $product
     * @param   float $price inputed product price
     * @param   bool $includingTax return price include tax flag
     * @param   null|Magento_Customer_Model_Address $shippingAddress
     * @param   null|Magento_Customer_Model_Address $billingAddress
     * @param   null|int $ctc customer tax class
     * @param   mixed $store
     * @param   bool $priceIncludesTax flag what price parameter contain tax
     * @return  float
     */
    public function getPrice($product, $price, $includingTax = null, $shippingAddress = null, $billingAddress = null,
        $ctc = null, $store = null, $priceIncludesTax = null
    ) {
        if (!$price) {
            return $price;
        }
        $store = Mage::app()->getStore($store);
        if (!$this->needPriceConversion($store)) {
            return $store->roundPrice($price);
        }
        if (is_null($priceIncludesTax)) {
            $priceIncludesTax = $this->priceIncludesTax($store);
        }

        $percent = $product->getTaxPercent();
        $includingPercent = null;

        $taxClassId = $product->getTaxClassId();
        if (is_null($percent)) {
            if ($taxClassId) {
                $request = Mage::getSingleton('Magento_Tax_Model_Calculation')
                    ->getRateRequest($shippingAddress, $billingAddress, $ctc, $store);
                $percent = Mage::getSingleton('Magento_Tax_Model_Calculation')
                    ->getRate($request->setProductClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludesTax) {
            $request = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRateRequest(false, false, false, $store);
            $includingPercent = Mage::getSingleton('Magento_Tax_Model_Calculation')
                ->getRate($request->setProductClassId($taxClassId));
        }

        if ($percent === false || is_null($percent)) {
            if ($priceIncludesTax && !$includingPercent) {
                return $price;
            }
        }

        $product->setTaxPercent($percent);

        if (!is_null($includingTax)) {
            if ($priceIncludesTax) {
                if ($includingTax) {
                    /**
                     * Recalculate price include tax in case of different rates
                     */
                    if ($includingPercent != $percent) {
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        /**
                         * Using regular rounding. Ex:
                         * price incl tax   = 52.76
                         * store tax rate   = 19.6%
                         * customer tax rate= 19%
                         *
                         * price excl tax = 52.76 / 1.196 = 44.11371237 ~ 44.11
                         * tax = 44.11371237 * 0.19 = 8.381605351 ~ 8.38
                         * price incl tax = 52.49531773 ~ 52.50 != 52.49
                         *
                         * that why we need round prices excluding tax before applying tax
                         * this calculation is used for showing prices on catalog pages
                         */
                        if ($percent != 0) {
                            $price = $this->getCalculator()->round($price);
                            $price = $this->_calculatePrice($price, $percent, true);
                        }
                    }
                } else {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                }
            } else {
                if ($includingTax) {
                    $price = $this->_calculatePrice($price, $percent, true);
                }
            }
        } else {
            if ($priceIncludesTax) {
                switch ($this->getPriceDisplayType($store)) {
                    case Magento_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    case Magento_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        break;

                    case Magento_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        $price = $this->_calculatePrice($price, $percent, true);
                        break;
                }
            } else {
                switch ($this->getPriceDisplayType($store)) {
                    case Magento_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX:
                        $price = $this->_calculatePrice($price, $percent, true);
                        break;

                    case Magento_Tax_Model_Config::DISPLAY_TYPE_BOTH:
                    case Magento_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX:
                        break;
                }
            }
        }
        return $store->roundPrice($price);
    }

    /**
     * Check if we have display in catalog prices including tax
     *
     * @return bool
     */
    public function displayPriceIncludingTax()
    {
        return $this->getPriceDisplayType() == Magento_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    /**
     * Check if we have display in catalog prices excluding tax
     *
     * @return bool
     */
    public function displayPriceExcludingTax()
    {
        return $this->getPriceDisplayType() == Magento_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    /**
     * Check if we have display in catalog prices including and excluding tax
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->getPriceDisplayType() == Magento_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Calculate price imcluding/excluding tax base on tax rate percent
     *
     * @param   float $price
     * @param   float $percent
     * @param   bool $type true - for calculate price including tax and false if price excluding tax
     * @return  float
     */
    protected function _calculatePrice($price, $percent, $type)
    {
        $calculator = Mage::getSingleton('Magento_Tax_Model_Calculation');
        if ($type) {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, false, false);
            return $price + $taxAmount;
        } else {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, true, false);
            return $price - $taxAmount;
        }
    }

    public function getIncExcTaxLabel($flag)
    {
        $text = $this->getIncExcText($flag);
        return $text ? ' <span class="tax-flag">('.$text.')</span>' : '';
    }

    public function shippingPriceIncludesTax($store = null)
    {
        return $this->_config->shippingPriceIncludesTax($store);
    }

    public function getShippingPriceDisplayType($store = null)
    {
        return $this->_config->getShippingPriceDisplayType($store);
    }

    public function displayShippingPriceIncludingTax()
    {
        return $this->getShippingPriceDisplayType() == Magento_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function displayShippingPriceExcludingTax()
    {
        return $this->getShippingPriceDisplayType() == Magento_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function displayShippingBothPrices()
    {
        return $this->getShippingPriceDisplayType() == Magento_Tax_Model_Config::DISPLAY_TYPE_BOTH;
    }

    public function getShippingTaxClass($store)
    {
        return $this->_config->getShippingTaxClass($store);
    }

    /**
     * Get shipping price
     *
     * @return float
     */
    public function getShippingPrice($price, $includingTax = null, $shippingAddress = null, $ctc = null, $store = null)
    {
        $pseudoProduct = new Magento_Object();
        $pseudoProduct->setTaxClassId($this->getShippingTaxClass($store));

        $billingAddress = false;
        if ($shippingAddress && $shippingAddress->getQuote() && $shippingAddress->getQuote()->getBillingAddress()) {
            $billingAddress = $shippingAddress->getQuote()->getBillingAddress();
        }

        $price = $this->getPrice(
            $pseudoProduct,
            $price,
            $includingTax,
            $shippingAddress,
            $billingAddress,
            $ctc,
            $store,
            $this->shippingPriceIncludesTax($store)
        );
        return $price;
    }

    public function getPriceTaxSql($priceField, $taxClassField)
    {
        if (!$this->priceIncludesTax() && $this->displayPriceExcludingTax()) {
            return '';
        }

        $request = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRateRequest(false, false, false);
        $defaultTaxes = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRatesForAllProductTaxClasses($request);

        $request = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRateRequest();
        $currentTaxes = Mage::getSingleton('Magento_Tax_Model_Calculation')->getRatesForAllProductTaxClasses($request);

        $defaultTaxString = $currentTaxString = '';

        $rateToVariable = array(
                            'defaultTaxString'=>'defaultTaxes',
                            'currentTaxString'=>'currentTaxes',
                            );
        foreach ($rateToVariable as $rateVariable=>$rateArray) {
            if ($$rateArray && is_array($$rateArray)) {
                $$rateVariable = '';
                foreach ($$rateArray as $classId=>$rate) {
                    if ($rate) {
                        $$rateVariable .= sprintf("WHEN %d THEN %12.4f ", $classId, $rate/100);
                    }
                }
                if ($$rateVariable) {
                    $$rateVariable = "CASE {$taxClassField} {$$rateVariable} ELSE 0 END";
                }
            }
        }

        $result = '';

        if ($this->priceIncludesTax()) {
            if ($defaultTaxString) {
                $result  = "-({$priceField}/(1+({$defaultTaxString}))*{$defaultTaxString})";
            }
            if (!$this->displayPriceExcludingTax() && $currentTaxString) {
                $result .= "+(({$priceField}{$result})*{$currentTaxString})";
            }
        } else {
            if ($this->displayPriceIncludingTax()) {
                if ($currentTaxString) {
                    $result .= "+({$priceField}*{$currentTaxString})";
                }
            }
        }
        return $result;
    }

    /**
     * Join tax class
     * @param Magento_DB_Select $select
     * @param int $storeId
     * @param string $priceTable
     * @return Magento_Tax_Helper_Data
     */
    public function joinTaxClass($select, $storeId, $priceTable = 'main_table')
    {
        $taxClassAttribute = Mage::getModel('Magento_Eav_Model_Entity_Attribute')
            ->loadByCode(Magento_Catalog_Model_Product::ENTITY, 'tax_class_id');
        $joinConditionD = implode(' AND ',array(
            "tax_class_d.entity_id = {$priceTable}.entity_id",
            $select->getAdapter()->quoteInto('tax_class_d.attribute_id = ?', (int)$taxClassAttribute->getId()),
            'tax_class_d.store_id = 0'
        ));
        $joinConditionC = implode(' AND ',array(
            "tax_class_c.entity_id = {$priceTable}.entity_id",
            $select->getAdapter()->quoteInto('tax_class_c.attribute_id = ?', (int)$taxClassAttribute->getId()),
            $select->getAdapter()->quoteInto('tax_class_c.store_id = ?', (int)$storeId)
        ));
        $select
            ->joinLeft(
                array('tax_class_d' => $taxClassAttribute->getBackend()->getTable()),
                $joinConditionD,
                array())
            ->joinLeft(
                array('tax_class_c' => $taxClassAttribute->getBackend()->getTable()),
                $joinConditionC,
                array());

        return $this;
    }

    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     *
     * @param   null|int $store
     * @return  0|1
     */
    public function discountTax($store=null)
    {
        return $this->_config->discountTax($store);
    }

    /**
     * Get value of "Apply Tax On" custom/original price configuration settings
     *
     * @param $store
     * @return 0|1
     */
    public function getTaxBasedOn($store = null)
    {
        return $this->_coreStoreConfig->getConfig(Magento_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON, $store);
    }

    /**
     * Check if tax can be applied to custom price
     *
     * @param $store
     * @return bool
     */
    public function applyTaxOnCustomPrice($store = null)
    {
        return ((int) $this->_coreStoreConfig->getConfig(Magento_Tax_Model_Config::CONFIG_XML_PATH_APPLY_ON, $store) == 0);
    }

    /**
     * Check if tax should be applied just to original price
     *
     * @param $store
     * @return bool
     */
    public function applyTaxOnOriginalPrice($store = null)
    {
        return ((int) $this->_coreStoreConfig->getConfig(Magento_Tax_Model_Config::CONFIG_XML_PATH_APPLY_ON, $store) == 1);
    }

    /**
     * Get taxes/discounts calculation sequence.
     * This sequence depends on "Catalog price include tax", "Apply Tax After Discount"
     * and "Apply Discount On Prices Including Tax" configuration options.
     *
     * @param   null|int|string|Magento_Core_Model_Store $store
     * @return  string
     */
    public function getCalculationSequence($store=null)
    {
        return $this->_config->getCalculationSequence($store);
    }

    /**
     * Get tax caclulation algorithm code
     *
     * @param   null|int $store
     * @return  string
     */
    public function getCalculationAgorithm($store=null)
    {
        return $this->_config->getAlgorithm($store);
    }

    /**
     * Get calculated taxes for each tax class
     *
     * This method returns array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'hidden_tax_amount' => $hiddenTaxAmount
     *      'title'             => $title
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param Magento_Sales_Model_Order $source
     * @return array
     */
    public function getCalculatedTaxes($source)
    {
        if ($this->_coreRegistry->registry('current_invoice')) {
            $current = $this->_coreRegistry->registry('current_invoice');
        } elseif ($this->_coreRegistry->registry('current_creditmemo')) {
            $current = $this->_coreRegistry->registry('current_creditmemo');
        } else {
            $current = $source;
        }

        $taxClassAmount = array();
        if ($current && $source) {
            foreach($current->getItemsCollection() as $item) {
                $taxCollection = Mage::getResourceModel('Magento_Tax_Model_Resource_Sales_Order_Tax_Item')
                    ->getTaxItemsByItemId(
                        $item->getOrderItemId() ? $item->getOrderItemId() : $item->getItemId()
                    );

                foreach ($taxCollection as $tax) {
                    $taxClassId = $tax['tax_id'];
                    $percent    = $tax['tax_percent'];

                    $price     = $item->getRowTotal();
                    $basePrice = $item->getBaseRowTotal();
                    if ($this->applyTaxAfterDiscount($item->getStoreId())) {
                        $price     = $price - $item->getDiscountAmount() + $item->getHiddenTaxAmount();
                        $basePrice = $basePrice - $item->getBaseDiscountAmount() + $item->getBaseHiddenTaxAmount();
                    }

                    if (isset($taxClassAmount[$taxClassId])) {
                        $taxClassAmount[$taxClassId]['tax_amount']      += $price * $percent / 100;
                        $taxClassAmount[$taxClassId]['base_tax_amount'] += $basePrice * $percent / 100;
                    } else {
                        $taxClassAmount[$taxClassId]['tax_amount']      = $price * $percent / 100;
                        $taxClassAmount[$taxClassId]['base_tax_amount'] = $basePrice * $percent / 100;
                        $taxClassAmount[$taxClassId]['title']           = $tax['title'];
                        $taxClassAmount[$taxClassId]['percent']         = $tax['percent'];
                    }
                }
            }

            foreach ($taxClassAmount as $key=>$tax) {
                 if ($tax['tax_amount'] == 0 && $tax['base_tax_amount'] == 0) {
                     unset($taxClassAmount[$key]);
                 }
            }

            $taxClassAmount = array_values($taxClassAmount);
        }

        return $taxClassAmount;
    }

    /**
     * Get calculated Shipping & Handling Tax
     *
     * This method returns array with format:
     * array(
     *  $index => array(
     *      'tax_amount'        => $taxAmount,
     *      'base_tax_amount'   => $baseTaxAmount,
     *      'hidden_tax_amount' => $hiddenTaxAmount
     *      'title'             => $title
     *      'percent'           => $percent
     *  )
     * )
     *
     * @param Magento_Sales_Model_Order $source
     * @return array
     */
    public function getShippingTax($source)
    {
        if ($this->_coreRegistry->registry('current_invoice')) {
            $current = $this->_coreRegistry->registry('current_invoice');
        } elseif ($this->_coreRegistry->registry('current_creditmemo')) {
            $current = $this->_coreRegistry->registry('current_creditmemo');
        } else {
            $current = $source;
        }

        $taxClassAmount = array();
        if ($current && $source) {
            if ($current->getShippingTaxAmount() != 0 && $current->getBaseShippingTaxAmount() != 0) {
                $taxClassAmount[0]['tax_amount']        = $current->getShippingTaxAmount();
                $taxClassAmount[0]['base_tax_amount']   = $current->getBaseShippingTaxAmount();
                if ($current->getShippingHiddenTaxAmount() > 0) {
                    $taxClassAmount[0]['hidden_tax_amount'] = $current->getShippingHiddenTaxAmount();
                }
                $taxClassAmount[0]['title']             = __('Shipping & Handling Tax');
                $taxClassAmount[0]['percent']           = NULL;
            }
        }

        return $taxClassAmount;
    }

    /**
     * Retrieve default customer tax class from config
     */
    public function getDefaultCustomerTaxClass()
    {
        return $this->_coreStoreConfig->getConfig(self::CONFIG_DEFAULT_CUSTOMER_TAX_CLASS);
    }

    /**
     * Retrieve default product tax class from config
     */
    public function getDefaultProductTaxClass()
    {
        return $this->_coreStoreConfig->getConfig(self::CONFIG_DEFAULT_PRODUCT_TAX_CLASS);
    }

}
