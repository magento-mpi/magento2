<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Catalog\Product\Type;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Giftcard extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_GIFTCARD = 'giftcard';

    /**
     * Whether product quantity is fractional number or not
     *
     * @var bool
     */
    protected $_canUseQtyDecimals = false;

    /**
     * Product is possible to configure
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * Store instance
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * Array of allowed giftcard amounts
     *
     * @var array
     */
    protected $_giftcardAmounts = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Logger $logger,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        PriceCurrencyInterface $priceCurrency,
        array $data = array()
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_store = $storeManager->getStore();
        $this->_localeFormat = $localeFormat;
        $this->priceCurrency = $priceCurrency;
        parent::__construct(
            $productFactory,
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $coreData,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $data
        );
    }

    /**
     * Check if gift card type is combined
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isTypeCombined($product)
    {
        if ($product->getGiftcardType() == \Magento\GiftCard\Model\Giftcard::TYPE_COMBINED) {
            return true;
        }
        return false;
    }

    /**
     * Check if gift card type is physical
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isTypePhysical($product)
    {
        if ($product->getGiftcardType() == \Magento\GiftCard\Model\Giftcard::TYPE_PHYSICAL) {
            return true;
        }
        return false;
    }

    /**
     * Check if gift card type is virtual
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isTypeVirtual($product)
    {
        if ($product->getGiftcardType() == \Magento\GiftCard\Model\Giftcard::TYPE_VIRTUAL) {
            return true;
        }
        return false;
    }

    /**
     * Check if gift card is virtual product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isVirtual($product)
    {
        return $product->getGiftcardType() == \Magento\GiftCard\Model\Giftcard::TYPE_VIRTUAL;
    }

    /**
     * Check if product is available for sale
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isSalable($product)
    {
        $amounts = $product->getPriceModel()->getAmounts($product);
        $open = $product->getAllowOpenAmount();

        if (!$open && !$amounts) {
            return false;
        }

        return parent::isSalable($product);
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Use standard preparation process and also add specific giftcard options.
     *
     * @param \Magento\Framework\Object $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(\Magento\Framework\Object $buyRequest, $product, $processMode)
    {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        try {
            $amount = $this->_validate($buyRequest, $product, $processMode);
        } catch (\Magento\Framework\Model\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            return __('Something went wrong  preparing the gift card.');
        }

        $product->addCustomOption('giftcard_amount', $amount, $product);
        $product->addCustomOption('giftcard_sender_name', $buyRequest->getGiftcardSenderName(), $product);
        $product->addCustomOption('giftcard_recipient_name', $buyRequest->getGiftcardRecipientName(), $product);
        if (!$this->isTypePhysical($product)) {
            $product->addCustomOption('giftcard_sender_email', $buyRequest->getGiftcardSenderEmail(), $product);
            $product->addCustomOption('giftcard_recipient_email', $buyRequest->getGiftcardRecipientEmail(), $product);
        }

        $messageAllowed = false;
        if ($product->getUseConfigAllowMessage()) {
            $messageAllowed = $this->_scopeConfig->isSetFlag(
                \Magento\GiftCard\Model\Giftcard::XML_PATH_ALLOW_MESSAGE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } else {
            $messageAllowed = (int)$product->getAllowMessage();
        }

        if ($messageAllowed) {
            $product->addCustomOption('giftcard_message', $buyRequest->getGiftcardMessage(), $product);
        }

        return $result;
    }

    /**
     * Validate Gift Card product, determine and return its amount
     *
     * @param \Magento\Framework\Object $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $processMode
     * @return mixed
     * @throws \Magento\Framework\Model\Exception
     */
    private function _validate(\Magento\Framework\Object $buyRequest, $product, $processMode)
    {
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);
        $allowedAmounts = $this->_getAllowedAmounts($product);
        $allowOpen = $product->getAllowOpenAmount();
        $selectedAmount = $buyRequest->getGiftcardAmount();
        $customAmount = $this->_getCustomGiftcardAmount($buyRequest);
        $this->_checkFields($buyRequest, $product, $isStrictProcessMode);

        $amount = null;
        if (($selectedAmount == 'custom' || !$selectedAmount) && $allowOpen) {
            if ($customAmount <= 0 && $isStrictProcessMode) {
                throw new \Magento\Framework\Model\Exception(__('Please specify a gift card amount.'));
            }
            $amount = $this->_getAmountWithinConstraints($product, $customAmount, $isStrictProcessMode);
        } elseif (is_numeric($selectedAmount)) {
            if (in_array($selectedAmount, $allowedAmounts)) {
                $amount = $selectedAmount;
            }
        }

        $amount = $this->_getAmountFromAllowed($amount, $allowedAmounts);

        if ($isStrictProcessMode) {
            $this->_checkGiftcardFields($buyRequest, $this->isTypePhysical($product), $amount);
        }
        return $amount;
    }

    /**
     * Get allowed giftcard amounts
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function _getAllowedAmounts($product)
    {
        if (is_null($this->_giftcardAmounts)) {
            $allowedAmounts = array();
            foreach ($product->getGiftcardAmounts() as $value) {
                $allowedAmounts[] = $this->priceCurrency->round($value['website_value']);
            }
            $this->_giftcardAmounts = $allowedAmounts;
        }
        return $this->_giftcardAmounts;
    }

    /**
     * Get custom amount if null
     *
     * @param mixed $amount
     * @param array $allowedAmounts
     * @return mixed|null
     */
    protected function _getAmountFromAllowed($amount, $allowedAmounts)
    {
        if (is_null($amount)) {
            if (count($allowedAmounts) == 1) {
                $amount = array_shift($allowedAmounts);
            }
        }
        return $amount;
    }

    /**
     * Check and count empty fields
     *
     * @param \Magento\Framework\Object $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $isStrictProcessMode
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _checkFields($buyRequest, $product, $isStrictProcessMode)
    {
        $emptyFields = $this->_countEmptyFields($buyRequest, $product);
        $selectedAmount = $buyRequest->getGiftcardAmount();
        $allowOpen = $product->getAllowOpenAmount();
        $allowedAmounts = $this->_getAllowedAmounts($product);
        $customAmount = $this->_getCustomGiftcardAmount($buyRequest);

        if (($selectedAmount == 'custom' || !$selectedAmount) && $allowOpen && $customAmount <= 0) {
            $emptyFields++;
        } elseif (is_numeric($selectedAmount)) {
            if (!in_array($selectedAmount, $allowedAmounts)) {
                $emptyFields++;
            }
        } elseif (count($allowedAmounts) != 1) {
            $emptyFields++;
        }

        if ($emptyFields > 1 && $isStrictProcessMode) {
            throw new \Magento\Framework\Model\Exception(__('Please specify all the required information.'));
        }
    }

    /**
     * Count empty fields
     *
     * @param \Magento\Framework\Object $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    protected function _countEmptyFields($buyRequest, $product)
    {
        $count = 0;
        if (!$buyRequest->getGiftcardRecipientName()) {
            $count++;
        }
        if (!$buyRequest->getGiftcardSenderName()) {
            $count++;
        }

        if (!$this->isTypePhysical($product)) {
            if (!$buyRequest->getGiftcardRecipientEmail()) {
                $count++;
            }
            if (!$buyRequest->getGiftcardSenderEmail()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Check whether amount is appropriate
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $customAmount
     * @param bool $isStrict
     * @return int
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _getAmountWithinConstraints($product, $customAmount, $isStrict)
    {
        $minAmount = $product->getOpenAmountMin();
        $maxAmount = $product->getOpenAmountMax();
        if (!$minAmount || $minAmount && $customAmount >= $minAmount) {
            if (!$maxAmount || $maxAmount && $customAmount <= $maxAmount) {
                return $customAmount;
            } elseif ($customAmount > $maxAmount && $isStrict) {
                $messageAmount = $this->_coreData->currency($maxAmount, true, false);
                throw new \Magento\Framework\Model\Exception(__('Gift Card max amount is %1', $messageAmount));
            }
        } elseif ($customAmount < $minAmount && $isStrict) {
            $messageAmount = $this->_coreData->currency($minAmount, true, false);
            throw new \Magento\Framework\Model\Exception(__('Gift Card min amount is %1', $messageAmount));
        }
    }

    /**
     * Fields check
     *
     * @param \Magento\Framework\Object $buyRequest
     * @param bool $isPhysical
     * @param int $amount
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _checkGiftcardFields($buyRequest, $isPhysical, $amount)
    {
        if (is_null($amount)) {
            throw new \Magento\Framework\Model\Exception(__('Please specify a gift card amount.'));
        }
        if (!$buyRequest->getGiftcardRecipientName()) {
            throw new \Magento\Framework\Model\Exception(__('Please specify a recipient name.'));
        }
        if (!$buyRequest->getGiftcardSenderName()) {
            throw new \Magento\Framework\Model\Exception(__('Please specify a sender name.'));
        }

        if (!$isPhysical) {
            if (!$buyRequest->getGiftcardRecipientEmail()) {
                throw new \Magento\Framework\Model\Exception(__('Please specify a recipient email.'));
            }
            if (!$buyRequest->getGiftcardSenderEmail()) {
                throw new \Magento\Framework\Model\Exception(__('Please specify a sender email.'));
            }
        }
    }

    /**
     * Get giftcard custom amount
     *
     * @param \Magento\Framework\Object $buyRequest
     * @return int|null
     */
    protected function _getCustomGiftcardAmount($buyRequest)
    {
        $customAmount = $buyRequest->getCustomGiftcardAmount();
        $rate = $this->_store->getCurrentCurrencyRate();
        if ($rate != 1 && $customAmount) {
            $customAmount = $this->_localeFormat->getNumber($customAmount);
            if (is_numeric($customAmount) && $customAmount) {
                $customAmount = $this->priceCurrency->round($customAmount / $rate);
            }
        }
        return $customAmount;
    }

    /**
     * Check if product can be bought
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    public function checkProductBuyState($product)
    {
        parent::checkProductBuyState($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof \Magento\Sales\Model\Quote\Item\Option) {
            $buyRequest = new \Magento\Framework\Object(unserialize($option->getValue()));
            $this->_validate($buyRequest, $product, self::PROCESS_MODE_FULL);
        }
        return $this;
    }

    /**
     * Sets flag that product has required options, because gift card always
     * has some required options, at least - recipient name
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function beforeSave($product)
    {
        parent::beforeSave($product);
        $product->setTypeHasOptions(true);
        $product->setTypeHasRequiredOptions(true);
        return $this;
    }

    /**
     * Prepare selected options for giftcard
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param  \Magento\Framework\Object $buyRequest
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $options = array(
            'giftcard_amount' => $buyRequest->getGiftcardAmount(),
            'custom_giftcard_amount' => $buyRequest->getCustomGiftcardAmount(),
            'giftcard_sender_name' => $buyRequest->getGiftcardSenderName(),
            'giftcard_sender_email' => $buyRequest->getGiftcardSenderEmail(),
            'giftcard_recipient_name' => $buyRequest->getGiftcardRecipientName(),
            'giftcard_recipient_email' => $buyRequest->getGiftcardRecipientEmail(),
            'giftcard_message' => $buyRequest->getGiftcardMessage()
        );

        return $options;
    }

    /**
     * Delete data specific for Gift Card product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
    }
}
