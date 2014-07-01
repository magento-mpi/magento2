<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Attribute;

/**
 * Tax attribute model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * Maximum number of tax rates per product supported by google shopping api
     */
    const RATES_MAX = 100;

    /**
     * @var \Magento\Tax\Helper\Data|null
     */
    protected $_taxData = null;

    /**
     * Config
     *
     * @var \Magento\GoogleShopping\Model\Config
     */
    protected $_config;

    /**
     * Tax Calculation Service
     *
     * @var \Magento\Tax\Service\V1\TaxCalculationService
     */
    protected $_taxCalculationService;

    /**
     * Quote Details Builder
     *
     * @var \Magento\Tax\Service\V1\Data\QuoteDetailsBuilder
     */
    protected $_quoteDetailsBuilder;

    /**
     * Quote Details Item Builder
     *
     * @var \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder
     */
    protected $_quoteDetailsItemBuilder;

    /**
     * Group Service Interface
     *
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService;

    /**
     * Default customer tax class
     *
     * @var int
     */
    protected $_defaultCustomerTaxClass;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GoogleShopping\Helper\Data $gsData
     * @param \Magento\GoogleShopping\Helper\Product $gsProduct
     * @param \Magento\Catalog\Model\Product\CatalogPrice $catalogPrice
     * @param \Magento\GoogleShopping\Model\Resource\Attribute $resource
     * @param \Magento\GoogleShopping\Model\Config $config
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Tax\Service\V1\TaxCalculationService $taxCalculationService
     * @param \Magento\Tax\Service\V1\Data\QuoteDetailsBuilder $quoteDetailsBuilder
     * @param \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder $quoteDetailsItemBuilder
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupServiceInterface
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GoogleShopping\Helper\Data $gsData,
        \Magento\GoogleShopping\Helper\Product $gsProduct,
        \Magento\Catalog\Model\Product\CatalogPrice $catalogPrice,
        \Magento\GoogleShopping\Model\Resource\Attribute $resource,
        \Magento\GoogleShopping\Model\Config $config,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Tax\Service\V1\TaxCalculationService $taxCalculationService,
        \Magento\Tax\Service\V1\Data\QuoteDetailsBuilder $quoteDetailsBuilder,
        \Magento\Tax\Service\V1\Data\QuoteDetails\ItemBuilder $quoteDetailsItemBuilder,
        \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupServiceInterface,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_config = $config;
        $this->_taxData = $taxData;
        $this->_taxCalculationService = $taxCalculationService;
        $this->_quoteDetailsBuilder = $quoteDetailsBuilder;
        $this->_quoteDetailsItemBuilder = $quoteDetailsItemBuilder;
        $this->_groupService = $groupServiceInterface;
        parent::__construct(
            $context,
            $registry,
            $productFactory,
            $gsData,
            $gsProduct,
            $catalogPrice,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Gdata\Gshopping\Entry $entry
     * @return \Magento\Framework\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {
        $entry->cleanTaxes();
        if ($this->_taxData->getConfig()->priceIncludesTax()) {
            return $entry;
        }

        $calc = $this->_taxData->getCalculator();

        $customerTaxClass = $this->getDefaultCustomerTaxClass($product->getStoreId());
        $rates = $calc->getRatesByCustomerAndProductTaxClasses($customerTaxClass, $product->getTaxClassId());

        $targetCountry = $this->_config->getTargetCountry($product->getStoreId());
        $ratesTotal = 0;

        foreach ($rates as $rate) {
            if ($targetCountry == $rate['country']) {
                //TODO: Retrieve the regions from a region_id, i.e. write getRegionsFromRegionId()
                $regions = getRegionsFromRegionId($rate->getRegionId());
                $ratesTotal += count($regions);
                if ($ratesTotal > self::RATES_MAX) {
                    throw new \Magento\Framework\Model\Exception(
                        __("Google shopping only supports %1 tax rates per product", self::RATES_MAX)
                    );
                }
                foreach ($regions as $region) {
                    try {
                        $product->getPriceInfo()->getAdjustment('tax');
                        $taxIncluded = True;
                    } catch(InvalidArgumentException $e) {
                        $taxIncluded = False;
                    }

                    $quoteDetailsItemDataArray = [
                        'code' => $product->getSku(),
                        'type' => 'product',
                        'tax_class_id' => $product->getTaxClassId(),
                        'unit_price' => $product->getPrice(),
                        'quantity' => $product->getQty(),
                        'tax_included' => $taxIncluded,
                        'short_description' => $product->getName(),
                    ];

                    $billingAddressDataArray = [
                        'country_id' => $rate->getTaxCountryId(),
                        'customer_id' => $customerTaxClass,
                        'region' => $region,
                        'postcode' => $rate->getTaxPostcode(),
                    ];

                    $shippingAddressDataArray = [
                        'country_id' => $rate->getTaxCountryId(),
                        'customer_id' => $customerTaxClass,
                        'region' => $region,
                        'postcode' => $rate->getTaxPostcode(),
                    ];

                    $quoteDetailsDataArray = [
                        'billing_address' => $billingAddressDataArray,
                        'shipping_address' => $shippingAddressDataArray,
                        'customer_tax_class_id' => $customerTaxClass,
                        'items' => [
                            $quoteDetailsItemDataArray,
                        ],
                    ];

                    $quoteDetailsItem = $this->_quoteDetailsBuilder
                        ->populateWithArray($quoteDetailsDataArray)
                        ->create();

                    $priceWithTax = $this->_taxCalculationService
                        ->calculateTax($quoteDetailsItem, $product->getStoreId());

                    $taxRate = ($priceWithTax->getTaxAmount() / $priceWithTax->getSubtotal()) * 100;

                    $entry->addTax(
                        [
                            'tax_rate' => $taxRate,
                            'tax_country' => $rate->getTaxCountryId(),
                            'tax_region' => $region,
                        ]
                    );
                }
            }
        }

        return $entry;
    }

    /**
     * Retrieve array of regions characterized by provided params
     *
     * @param string $state
     * @param string $zip
     * @return string[]
     */
    protected function _parseRegions($state, $zip)
    {
        return !empty($zip) && $zip != '*' ? $this->_parseZip($zip) : ($state ? array($state) : array('*'));
    }

    /**
     * Retrieve array of regions characterized by provided zip code
     *
     * @param string $zip
     * @return string[]
     */
    protected function _parseZip($zip)
    {
        if (strpos($zip, '-') == -1) {
            return array($zip);
        } else {
            return $this->zipRangeToZipPattern($zip);
        }
    }

    /**
     * Convert Magento zip range to array of Google Shopping zip-patterns
     * (e.g., 12000-13999 -> [12*, 13*])
     *
     * @param  string $zipRange
     * @return array
     */
    private function zipRangeToZipPattern($zipRange)
    {
        $zipLength = 5;
        $zipPattern = array();

        if (!preg_match("/^(.+)-(.+)$/", $zipRange, $zipParts)) {
            return array($zipRange);
        }

        if ($zipParts[1] == $zipParts[2]) {
            return array($zipParts[1]);
        }

        if ($zipParts[1] > $zipParts[2]) {
            list($zipParts[2], $zipParts[1]) = array($zipParts[1], $zipParts[2]);
        }

        $from = str_split($zipParts[1]);
        $to = str_split($zipParts[2]);

        $startZip = '';
        $diffPosition = null;
        for ($pos = 0; $pos < $zipLength; $pos++) {
            if ($from[$pos] == $to[$pos]) {
                $startZip .= $from[$pos];
            } else {
                $diffPosition = $pos;
                break;
            }
        }

        /*
         * calculate zip-patterns
         */
        if (min(array_slice($to, $diffPosition)) == 9 && max(array_slice($from, $diffPosition)) == 0) {
            // particular case like 11000-11999 -> 11*
            return array($startZip . '*');
        } else {
            // calculate approximate zip-patterns
            $start = $from[$diffPosition];
            $finish = $to[$diffPosition];
            if ($diffPosition < $zipLength - 1) {
                $start++;
                $finish--;
            }
            $end = $diffPosition < $zipLength - 1 ? '*' : '';
            for ($digit = $start; $digit <= $finish; $digit++) {
                $zipPattern[] = $startZip . $digit . $end;
            }
        }

        if ($diffPosition == $zipLength - 1) {
            return $zipPattern;
        }

        $nextAsteriskFrom = true;
        $nextAsteriskTo = true;
        for ($pos = $zipLength - 1; $pos > $diffPosition; $pos--) {
            // calculate zip-patterns based on $from value
            if ($from[$pos] == 0 && $nextAsteriskFrom) {
                $nextAsteriskFrom = true;
            } else {
                $subZip = '';
                for ($k = $diffPosition; $k < $pos; $k++) {
                    $subZip .= $from[$k];
                }
                $delta = $nextAsteriskFrom ? 0 : 1;
                $end = $pos < $zipLength - 1 ? '*' : '';
                for ($i = $from[$pos] + $delta; $i <= 9; $i++) {
                    $zipPattern[] = $startZip . $subZip . $i . $end;
                }
                $nextAsteriskFrom = false;
            }

            // calculate zip-patterns based on $to value
            if ($to[$pos] == 9 && $nextAsteriskTo) {
                $nextAsteriskTo = true;
            } else {
                $subZip = '';
                for ($k = $diffPosition; $k < $pos; $k++) {
                    $subZip .= $to[$k];
                }
                $delta = $nextAsteriskTo ? 0 : 1;
                $end = $pos < $zipLength - 1 ? '*' : '';
                for ($i = 0; $i <= $to[$pos] - $delta; $i++) {
                    $zipPattern[] = $startZip . $subZip . $i . $end;
                }
                $nextAsteriskTo = false;
            }
        }

        if ($nextAsteriskFrom) {
            $zipPattern[] = $startZip . $from[$diffPosition] . '*';
        }
        if ($nextAsteriskTo) {
            $zipPattern[] = $startZip . $to[$diffPosition] . '*';
        }

        return $zipPattern;
    }

    /**
     * Fetch default customer tax class
     *
     * @param null|Store|string|int $store
     * @return int
     */
    private function getDefaultCustomerTaxClass($store = null)
    {
        if (is_null($this->_defaultCustomerTaxClass)) {
            //Not catching the exception here since default group is expected
            $defaultCustomerGroup = $this->_groupService->getDefaultGroup($store);
            $this->_defaultCustomerTaxClass = $defaultCustomerGroup->getTaxClassId();
        }
        return $this->_defaultCustomerTaxClass;
    }
}
