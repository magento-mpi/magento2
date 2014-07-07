<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Attribute;

use Magento\Tax\Service\V1\Data\TaxRate;
use Magento\Tax\Service\V1\Data\TaxRule;

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
     * filterBuilder
     *
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $_filterBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * TaxRuleService
     *
     * @var \Magento\Tax\Service\V1\TaxRuleServiceInterface
     */
    protected $_taxRuleService;

    /**
     * TaxRateService
     *
     * @var \Magento\Tax\Service\V1\TaxRateServiceInterface
     */
    protected $_taxRateService;

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
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Tax\Service\V1\TaxRuleServiceInterface $taxRuleService
     * @param \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService
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
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Tax\Service\V1\TaxRuleServiceInterface $taxRuleService,
        \Magento\Tax\Service\V1\TaxRateServiceInterface $taxRateService,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_config = $config;
        $this->_taxData = $taxData;
        $this->_taxCalculationService = $taxCalculationService;
        $this->_quoteDetailsBuilder = $quoteDetailsBuilder;
        $this->_quoteDetailsItemBuilder = $quoteDetailsItemBuilder;
        $this->_groupService = $groupServiceInterface;
        $this->_filterBuilder = $filterBuilder;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_taxRuleService = $taxRuleService;
        $this->_taxRateService = $taxRateService;
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
     * @throws \Magento\Framework\Model\Exception
     */
    public function convertAttribute($product, $entry)
    {
        $entry->cleanTaxes();
        if ($this->_taxData->getConfig()->priceIncludesTax()) {
            return $entry;
        }

        $customerTaxClassId = $this->_getDefaultCustomerTaxClassId($product->getStoreId());
        $rates = $this->_getRatesByCustomerAndProductTaxClassId($customerTaxClassId, $product->getTaxClassId());
        $targetCountry = $this->_config->getTargetCountry($product->getStoreId());
        $ratesTotal = 0;
        foreach ($rates as $rate) {
            $countryId = $rate->getCountryId();
            $postcode = $rate->getPostcode();
            if ($targetCountry == $countryId) {
                $regions = $this->_getRegionsByRegionId($rate->getRegionId(), $postcode);
                $ratesTotal += count($regions);
                if ($ratesTotal > self::RATES_MAX) {
                    throw new \Magento\Framework\Model\Exception(
                        __("Google shopping only supports %1 tax rates per product", self::RATES_MAX)
                    );
                }
                foreach ($regions as $region) {
                    try {
                        $product->getPriceInfo()->getAdjustment('tax');
                        $taxIncluded = true;
                    } catch (InvalidArgumentException $e) {
                        $taxIncluded = false;
                    }

                    $quoteDetailsItemDataArray = [
                        'code' => $product->getSku(),
                        'type' => 'product',
                        'tax_class_id' => $product->getTaxClassId(),
                        'unit_price' => $product->getPrice(),
                        'quantity' => 1,
                        'tax_included' => $taxIncluded,
                        'short_description' => $product->getName(),
                    ];

                    $billingAddressDataArray = [
                        'country_id' => $countryId,
                        'customer_id' => $customerTaxClassId,
                        'region' => ['region_id' => $rate->getRegionId()],
                        'postcode' => $postcode,
                    ];

                    $shippingAddressDataArray = [
                        'country_id' => $countryId,
                        'customer_id' => $customerTaxClassId,
                        'region' => ['region_id' => $rate->getRegionId()],
                        'postcode' => $postcode,
                    ];

                    $quoteDetailsDataArray = [
                        'billing_address' => $billingAddressDataArray,
                        'shipping_address' => $shippingAddressDataArray,
                        'customer_tax_class_id' => $customerTaxClassId,
                        'items' => [
                            $quoteDetailsItemDataArray,
                        ],
                    ];

                    $quoteDetailsObject = $this->_quoteDetailsBuilder
                        ->populateWithArray($quoteDetailsDataArray)
                        ->create();

                    $taxDetails = $this->_taxCalculationService
                        ->calculateTax($quoteDetailsObject, $product->getStoreId());

                    $taxRate = ($taxDetails->getTaxAmount() / $taxDetails->getSubtotal()) * 100;

                    $entry->addTax(
                        [
                            'tax_rate' => $taxRate,
                            'tax_country' => $countryId,
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
            return $this->_zipRangeToZipPattern($zip);
        }
    }

    /**
     * Convert Magento zip range to array of Google Shopping zip-patterns
     * (e.g., 12000-13999 -> [12*, 13*])
     *
     * @param  string $zipRange
     * @return array
     */
    private function _zipRangeToZipPattern($zipRange)
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
    private function _getDefaultCustomerTaxClassId($store = null)
    {
        if (is_null($this->_defaultCustomerTaxClass)) {
            //Not catching the exception here since default group is expected
            $defaultCustomerGroup = $this->_groupService->getDefaultGroup($store);
            $this->_defaultCustomerTaxClass = $defaultCustomerGroup->getTaxClassId();
        }
        return $this->_defaultCustomerTaxClass;
    }

    /**
     * Get rates by customer and product classes
     *
     * @param int $customerTaxClassId
     * @param int $productTaxClassId
     * @return TaxRate[]
     */
    private function _getRatesByCustomerAndProductTaxClassId($customerTaxClassId, $productTaxClassId)
    {
        $filters = [
            $this->_filterBuilder->setField(TaxRule::CUSTOMER_TAX_CLASS_IDS)->setValue([$customerTaxClassId])->create(),
            $this->_filterBuilder->setField(TaxRule::PRODUCT_TAX_CLASS_IDS)->setValue([$productTaxClassId])->create(),

        ];
        $searchResults = $this->_taxRuleService->searchTaxRules(
            $this->_searchCriteriaBuilder->addFilter($filters)->create()
        );
        $taxRules = $searchResults->getItems();
        $rates = [];
        foreach ($taxRules as $taxRule) {
            $rateIds = $taxRule->getTaxRateIds();
            if (!empty($rateIds)) {
                foreach ($rateIds as $ratId) {
                    $rates[] = $this->_taxRateService->getTaxRate($ratId);
                }
            }
        }
        return $rates;
    }

    /**
     * Get regions by region ID
     *
     * @param int $regionId
     * @param string $postalCode
     * @return String[]
     */
    private function _getRegionsByRegionId($regionId, $postalCode)
    {
        $regions = [];
        $resource = $this->_getResource();
        $adapter = $resource->getReadConnection();
        $selectCSP = $adapter->select();
        $selectCSP->from(
            ['main_table' => $resource->getTable('directory_country_region')],
            ['state' => 'main_table.code']
        )->where("main_table.region_id = $regionId");

        $dbResult = $adapter->fetchAll($selectCSP);
        if (!empty($dbResult)) {
            $state = $dbResult[0]['state'];
            $regions = $this->_parseRegions($state, $postalCode);
        }
        return $regions;
    }
}
