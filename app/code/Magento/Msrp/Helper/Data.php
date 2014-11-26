<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Msrp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Msrp\Model\Product\Attribute\Source\Type;
use Magento\Framework\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;

/**
 * Msrp data helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Msrp\Model\Product\Options
     */
    protected $productOptions;

    /**
     * @var \Magento\Msrp\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param Context $context
     * @param ProductFactory $productFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Msrp\Model\Product\Options $productOptions
     * @param \Magento\Msrp\Model\Msrp $msrp
     * @param \Magento\Msrp\Model\Config $config
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        \Magento\Msrp\Model\Product\Options $productOptions,
        \Magento\Msrp\Model\Msrp $msrp,
        \Magento\Msrp\Model\Config $config,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->productOptions = $productOptions;
        $this->msrp = $msrp;
        $this->config = $config;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Check if can apply Minimum Advertise price to product
     * in specific visibility
     *
     * @param int|Product $product
     * @param int|null $visibility Check displaying price in concrete place (by default generally)
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function canApplyMsrp($product, $visibility = null)
    {
        if (!$this->config->isEnabled()) {
            return false;
        }
        if (is_numeric($product)) {
            $product = $this->productFactory->create()
                ->setStoreId($this->storeManager->getStore()->getId())
                ->load($product);
        }
        $result = $this->msrp->canApplyToProduct($product);
        if ($result && $visibility !== null) {
            $productPriceVisibility = $product->getMsrpDisplayActualPriceType();
            if ($productPriceVisibility == Type\Price::TYPE_USE_CONFIG) {
                $productPriceVisibility = $this->config->getDisplayActualPriceType();
            }
            $result = $productPriceVisibility == $visibility;
        }

        if ($product->getTypeInstance()->isComposite($product) && (!$result || $visibility !== null)) {
            $isEnabledInOptions = $this->productOptions->isEnabled($product, $visibility);
            if ($isEnabledInOptions !== null) {
                $result = $isEnabledInOptions;
            }
        }

        return $result;
    }

    /**
     * Get Msrp message for price
     *
     * @param Product $product
     * @return string
     */
    public function getMsrpPriceMessage($product)
    {
        $message = "";
        if ($this->canApplyMsrp($product, Type::TYPE_IN_CART)) {
            $message = __('To see product price, add this item to your cart. You can always remove it later.');
        } elseif ($this->canApplyMsrp($product, Type::TYPE_BEFORE_ORDER_CONFIRM)) {
            $message = __('See price before order confirmation.');
        }
        return $message;
    }

    /**
     * Check is product need gesture to show price
     *
     * @param int|Product $product
     * @return bool
     */
    public function isShowPriceOnGesture($product)
    {
        return $this->canApplyMsrp($product, Type::TYPE_ON_GESTURE);
    }

    /**
     * @param int|Product $product
     * @return bool
     */
    public function isShowBeforeOrderConfirm($product)
    {
        return $this->canApplyMsrp($product, Type::TYPE_BEFORE_ORDER_CONFIRM);
    }

    /**
     * @param int|Product $product
     * @return bool|float
     */
    public function isMinimalPriceLessMsrp($product)
    {
        if (is_numeric($product)) {
            $product = $this->productFactory->create()
                ->setStoreId($this->storeManager->getStore()->getId())
                ->load($product);
        }
        $msrp = $product->getMsrp();
        $price = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE);
        if ($msrp === null) {
            if ($product->getTypeId() !== \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
                return false;
            } else {
                $msrp = $product->getTypeInstance()->getChildrenMsrp($product);
            }
        }
        if ($msrp) {
            $msrp = $this->priceCurrency->convertAndRound($msrp);
        }
        return $msrp > $price->getValue();
    }
}
