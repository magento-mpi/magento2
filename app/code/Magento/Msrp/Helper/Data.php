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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Resource\Eav\AttributeFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product;

/**
 * Msrp data helper
 */
class Data extends AbstractHelper
{
    /**
     * Minimum advertise price constants
     */
    const XML_PATH_MSRP_ENABLED = 'sales/msrp/enabled';
    const XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE = 'sales/msrp/display_price_type';
    const XML_PATH_MSRP_EXPLANATION_MESSAGE = 'sales/msrp/explanation_message';
    const XML_PATH_MSRP_EXPLANATION_MESSAGE_WHATS_THIS = 'sales/msrp/explanation_message_whats_this';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $storeId;

    /**
     * @var ProductOptions
     */
    protected $productOptions;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Escaper $escaper
     * @param ProductFactory $productFactory
     * @param StoreManagerInterface $storeManager
     * @param AttributeFactory $eavAttributeFactory
     * @param \Magento\Msrp\Model\Product\Options $productOptions
     * @param \Magento\Msrp\Model\Msrp $msrp
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Escaper $escaper,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        \Magento\Msrp\Model\Product\Options $productOptions,
        \Magento\Msrp\Model\Msrp $msrp
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->escaper = $escaper;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->productOptions = $productOptions;
        $this->msrp = $msrp;
    }

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->storeId = $store;
        return $this;
    }

    /**
     * Check if Minimum Advertised Price is enabled
     *
     * @return bool
     */
    public function isMsrpEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_MSRP_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Return MAP display actual type
     *
     * @return null|string
     */
    public function getMsrpDisplayActualPriceType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Check if can apply Minimum Advertise price to product
     * in specific visibility
     *
     * @param int|\Magento\Catalog\Model\Product $product
     * @param int|null $visibility Check displaying price in concrete place (by default generally)
     * @return bool
     */
    public function canApplyMsrp($product, $visibility = null)
    {
        if (!$this->isMsrpEnabled()) {
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
                $productPriceVisibility = $this->getMsrpDisplayActualPriceType();
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
     * Get MAP message for price
     *
     * @param \Magento\Catalog\Model\Product $product
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
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isShowPriceOnGesture($product)
    {
        return $this->canApplyMsrp($product, Type::TYPE_ON_GESTURE);
    }

    public function isShowBeforeOrderConfirm($product)
    {
        return $this->canApplyMsrp($product, Type::TYPE_BEFORE_ORDER_CONFIRM);
    }
}
