<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Catalog\Product\View\Type;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Tax\Service\V1\TaxCalculationServiceInterface;

/**
 * Catalog bundle product info block
 * 
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Bundle extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * constants for different rounding methods
     */
    const UNIT_ROUNDING = 0;
    const ROW_ROUNDING = 1;
    const TOTAL_ROUNDING = 2;

    /**
     * Mapping between constants in \Magento\Tax\Model\Calculation and this class
     *
     * @var array
     */
    protected $mapping = [
        TaxCalculationServiceInterface::CALC_UNIT_BASE => self::UNIT_ROUNDING,
        TaxCalculationServiceInterface::CALC_ROW_BASE => self::ROW_ROUNDING,
        TaxCalculationServiceInterface::CALC_TOTAL_BASE => self::TOTAL_ROUNDING,
    ];

    /**
     * @var array
     */
    protected $_options;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProduct;

    /**
     * @var \Magento\Bundle\Model\Product\PriceFactory
     */
    protected $_productPrice;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Bundle\Model\Product\PriceFactory $productPrice
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Bundle\Model\Product\PriceFactory $productPrice,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_catalogProduct = $catalogProduct;
        $this->_productPrice = $productPrice;
        $this->priceCurrency = $priceCurrency;
        $this->jsonEncoder = $jsonEncoder;
        $this->_localeFormat = $localeFormat;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (!$this->_options) {
            $product = $this->getProduct();
            $typeInstance = $product->getTypeInstance();
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $typeInstance->getOptionsCollection($product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($product),
                $product
            );

            $this->_options = $optionCollection->appendSelections(
                $selectionCollection,
                false,
                $this->_catalogProduct->getSkipSaleableCheck()
            );
        }

        return $this->_options;
    }

    /**
     * @return bool
     */
    public function hasOptions()
    {
        $this->getOptions();
        if (empty($this->_options) || !$this->getProduct()->isSalable()) {
            return false;
        }
        return true;
    }

    /**
     * Returns JSON encoded config to be used in JS scripts
     *
     * @return string
     * 
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getJsonConfig()
    {
        /** @var \Magento\Bundle\Model\Option[] $optionsArray */
        $optionsArray = $this->getOptions();
        $options = array();
        $selected = array();
        $currentProduct = $this->getProduct();

        if ($preConfiguredFlag = $currentProduct->hasPreconfiguredValues()) {
            $preConfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues = array();
        }


        $position = 0;
        foreach ($optionsArray as $optionItem) {
            /* @var $optionItem \Magento\Bundle\Model\Option */
            if (!$optionItem->getSelections()) {
                continue;
            }

            $optionId = $optionItem->getId();
            $option = array(
                'selections' => array(),
                'title' => $optionItem->getTitle(),
                'isMulti' => in_array($optionItem->getType(), array('multi', 'checkbox')),
                'position' => $position++
            );

            $selectionCount = count($optionItem->getSelections());

            foreach ($optionItem->getSelections() as $selectionItem) {
                /* @var $selectionItem \Magento\Catalog\Model\Product */
                $selectionId = $selectionItem->getSelectionId();
                $qty = !($selectionItem->getSelectionQty() * 1) ? '1' : $selectionItem->getSelectionQty() * 1;
                // recalculate currency
                $tierPrices = $selectionItem->getPriceInfo()
                    ->getPrice(\Magento\Catalog\Pricing\Price\TierPrice::PRICE_CODE)
                    ->getTierPriceList();

                foreach ($tierPrices as &$tierPriceInfo) {
                    /** @var \Magento\Framework\Pricing\Amount\Base $price */
                    $price = $tierPriceInfo['price'];

                    $priceBaseAmount = $price->getBaseAmount();
                    $priceValue = $price->getValue();

                    $bundleProductPrice = $this->_productPrice->create();
                    $priceBaseAmount = $bundleProductPrice->getLowestPrice($currentProduct, $priceBaseAmount);
                    $priceValue = $bundleProductPrice->getLowestPrice($currentProduct, $priceValue);

                    $tierPriceInfo['prices'] = [
                        'oldPrice' => [
                            'amount' => $this->priceCurrency->convert($priceBaseAmount)
                        ],
                        'basePrice' => [
                            'amount' => $this->priceCurrency->convert($priceBaseAmount)
                        ],
                        'finalPrice' => [
                            'amount' => $this->priceCurrency->convert($priceValue)
                        ]
                    ];
                }
                // break the reference with the last element


                $bundleOptionPriceAmount = $currentProduct->getPriceInfo()->getPrice('bundle_option')
                    ->getOptionSelectionAmount($selectionItem);
                $finalPrice = $bundleOptionPriceAmount->getValue();
                $basePrice = $bundleOptionPriceAmount->getBaseAmount();

                $selection = array(
                    'qty' => $qty,
                    'customQty' => $selectionItem->getSelectionCanChangeQty(),
                    'prices' => [
                        'oldPrice' => [
                            'amount' => $this->priceCurrency->convert($basePrice) // todo: calculate for regular price
                        ],
                        'basePrice' => [
                            'amount' => $this->priceCurrency->convert($basePrice)
                        ],
                        'finalPrice' => [
                            'amount' => $this->priceCurrency->convert($finalPrice)
                        ]
                    ],
                    'priceType' => $selectionItem->getSelectionPriceType(),
                    'tierPrice' => $tierPrices,
                    'name' => $selectionItem->getName(),
                    'canApplyMsrp' => false
                );

                $responseObject = new \Magento\Framework\Object();
                $args = array('response_object' => $responseObject, 'selection' => $selectionItem);
                $this->_eventManager->dispatch('bundle_product_view_config', $args);
                if (is_array($responseObject->getAdditionalOptions())) {
                    foreach ($responseObject->getAdditionalOptions() as $index => $value) {
                        $selection[$index] = $value;
                    }
                }
                $option['selections'][$selectionId] = $selection;

                if (($selectionItem->getIsDefault() || $selectionCount == 1 && $optionItem->getRequired())
                    && $selectionItem->isSalable()
                ) {
                    $selected[$optionId][] = $selectionId;
                }
            }
            $options[$optionId] = $option;

            // Add attribute default value (if set)
            if ($preConfiguredFlag) {
                $configValue = $preConfiguredValues->getData('bundle_option/' . $optionId);
                if ($configValue) {
                    $defaultValues[$optionId] = $configValue;
                }
            }
        }
        $isFixedPrice = $this->getProduct()->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED;

        $productAmount = $currentProduct
            ->getPriceInfo()
            ->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)
            ->getPriceWithoutOption();

        $baseProductAmount = $currentProduct
            ->getPriceInfo()
            ->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)
            ->getAmount();

        $config = array(
            'options' => $options,
            'selected' => $selected,
            'bundleId' => $currentProduct->getId(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->priceCurrency->convert($baseProductAmount->getValue())
                ],
                'basePrice' => [
                    'amount' => $isFixedPrice ? $this->priceCurrency->convert($productAmount->getBaseAmount()) : 0
                ],
                'finalPrice' => [
                    'amount' => $isFixedPrice ? $this->priceCurrency->convert($productAmount->getValue()) : 0
                ]
            ],
            'priceType' => $currentProduct->getPriceType(),
            'includeTax' => $this->_taxData->priceIncludesTax() ? 'true' : 'false',
            'isFixedPrice' => $isFixedPrice,
        );

        if ($preConfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Get html for option
     *
     * @param \Magento\Bundle\Model\Option $option
     * @return string
     */
    public function getOptionHtml($option)
    {
        $optionBlock = $this->getChildBlock($option->getType());
        if (!$optionBlock) {
            return __('There is no defined renderer for "%1" option type.', $option->getType());
        }
        return $optionBlock->setOption($option)->toHtml();
    }

    /**
     * Return the rounding method based on tax calculation
     * This is a workaround as the proper way is to always call tax service to get taxed price
     *
     * @return int
     */
    public function getRoundingMethod()
    {
        $algorithm = $this->_taxData->getCalculationAgorithm();
        return isset($this->mapping[$algorithm]) ? $this->mapping[$algorithm] : self::TOTAL_ROUNDING;
    }
}
