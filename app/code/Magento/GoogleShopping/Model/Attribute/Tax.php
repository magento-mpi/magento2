<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Attribute;

use Magento\Store\Model\Store;
use Magento\Framework\Parse\Zip;

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
     * GoogleShopping Helper
     *
     * @var \Magento\GoogleShopping\Helper\Data
     */
    protected $_googleShoppingHelper;

    /**
     * Region  model
     *
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

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
     * @param \Magento\GoogleShopping\Helper\Data $googleShoppingHelper
     * @param \Magento\Directory\Model\Region $regionFactory
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
        \Magento\GoogleShopping\Helper\Data $googleShoppingHelper,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_config = $config;
        $this->_taxData = $taxData;
        $this->_taxCalculationService = $taxCalculationService;
        $this->_quoteDetailsBuilder = $quoteDetailsBuilder;
        $this->_quoteDetailsItemBuilder = $quoteDetailsItemBuilder;
        $this->_googleShoppingHelper = $googleShoppingHelper;
        $this->_groupService = $groupServiceInterface;
        $this->_regionFactory = $regionFactory;
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

        $defaultCustomerTaxClassId = $this->_getDefaultCustomerTaxClassId($product->getStoreId());
        $rates = $this->_googleShoppingHelper
            ->getRatesByCustomerAndProductTaxClassId($defaultCustomerTaxClassId, $product->getTaxClassId());
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
                        'customer_id' => $defaultCustomerTaxClassId,
                        'region' => ['region_id' => $rate->getRegionId()],
                        'postcode' => $postcode,
                    ];

                    $shippingAddressDataArray = [
                        'country_id' => $countryId,
                        'customer_id' => $defaultCustomerTaxClassId,
                        'region' => ['region_id' => $rate->getRegionId()],
                        'postcode' => $postcode,
                    ];

                    $quoteDetailsDataArray = [
                        'billing_address' => $billingAddressDataArray,
                        'shipping_address' => $shippingAddressDataArray,
                        'customer_tax_class_id' => $defaultCustomerTaxClassId,
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
     * Get regions by region ID
     *
     * @param int $regionId
     * @param string $postalCode
     * @return String[]
     */
    private function _getRegionsByRegionId($regionId, $postalCode)
    {
        $regions = [];
        $regionModel = $this->_regionFactory->create();
        $regionData = $regionModel->load($regionId);
        if (!empty($regionData)) {
            $regions = Zip::parseRegions($regionData['code'], $postalCode);
        }
        return $regions;
    }
}
