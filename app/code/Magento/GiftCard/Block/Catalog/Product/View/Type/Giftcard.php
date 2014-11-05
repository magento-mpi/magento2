<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Block\Catalog\Product\View\Type;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Giftcard extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Customer\Model\Session $customerSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Customer\Model\Session $customerSession,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
        $this->_isScopePrivate = true;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getAmountSettingsJson($product)
    {
        $result = array('min' => 0, 'max' => 0);
        if ($product->getAllowOpenAmount()) {
            if ($v = $product->getOpenAmountMin()) {
                $result['min'] = $v;
            }
            if ($v = $product->getOpenAmountMax()) {
                $result['max'] = $v;
            }
        }
        return $result;
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isConfigured($product)
    {
        if (!$product->getAllowOpenAmount() && !$product->getGiftcardAmounts()) {
            return false;
        }
        return true;
    }

    /**
     * @param Product $product
     * @return bool
     *
     * @deprecated \Magento\GiftCard\Pricing\Render\FinalPriceBox::isOpenAmountAvailable
     */
    public function isOpenAmountAvailable($product)
    {
        if (!$product->getAllowOpenAmount()) {
            return false;
        }
        return true;
    }

    /**
     * @param Product $product
     * @return bool
     *
     * @deprecated \Magento\GiftCard\Pricing\Render\FinalPriceBox::isAmountAvailable
     */
    public function isAmountAvailable($product)
    {
        if (!$product->getGiftcardAmounts()) {
            return false;
        }
        return true;
    }

    /**
     * @param Product $product
     * @return array
     *
     * @deprecated \Magento\GiftCard\Pricing\Render\FinalPriceBox::getAmounts
     */
    public function getAmounts($product)
    {
        $result = array();
        foreach ($product->getGiftcardAmounts() as $amount) {
            $result[] = $this->priceCurrency->round($amount['website_value']);
        }
        sort($result);
        return $result;
    }

    /**
     * @return string
     *
     * @deprecated \Magento\GiftCard\Pricing\Render\FinalPriceBox::getCurrentCurrency
     */
    public function getCurrentCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @param Product $product
     * @return bool|int
     */
    public function isMessageAvailable($product)
    {
        if ($product->getUseConfigAllowMessage()) {
            return $this->_scopeConfig->isSetFlag(
                \Magento\GiftCard\Model\Giftcard::XML_PATH_ALLOW_MESSAGE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } else {
            return (int)$product->getAllowMessage();
        }
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isEmailAvailable($product)
    {
        if ($product->getTypeInstance()->isTypePhysical($product)) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        $firstName = $this->_customerSession->getCustomerDataObject()->getFirstname();
        $lastName = $this->_customerSession->getCustomerDataObject()->getLastname();

        if ($this->checkoutSession->getData('giftcard_sender_name')) {
            return $this->checkoutSession->getData('giftcard_sender_name', true);
        } elseif ($firstName && $lastName) {
            return $firstName . ' ' . $lastName;
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        if ($this->checkoutSession->getData('giftcard_sender_email')) {
            return $this->checkoutSession->getData('giftcard_sender_email', true);
        }
        return $this->_customerSession->getCustomerDataObject()->getEmail();
    }

    /**
     * @return int
     */
    public function getMessageMaxLength()
    {
        return (int)$this->_scopeConfig->getValue(
            \Magento\GiftCard\Model\Giftcard::XML_PATH_MESSAGE_MAX_LENGTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns default value to show in input
     *
     * @param string $key
     * @return string
     */
    public function getDefaultValue($key)
    {
        if ($this->checkoutSession->getData($key)) {
            return $this->checkoutSession->getData($key, true);
        }
        return $this->getProduct()->getPreconfiguredValues()->getData($key);
    }

    /**
     * Returns default sender name to show in input
     *
     * @return string
     */
    public function getDefaultSenderName()
    {
        $senderName = $this->getProduct()->getPreconfiguredValues()->getData('giftcard_sender_name');
        if (!strlen($senderName)) {
            $senderName = $this->getCustomerName();
        }
        return $senderName;
    }

    /**
     * Returns default sender email to show in input
     *
     * @return string
     */
    public function getDefaultSenderEmail()
    {
        $senderEmail = $this->getProduct()->getPreconfiguredValues()->getData('giftcard_sender_email');
        if (!strlen($senderEmail)) {
            $senderEmail = $this->getCustomerEmail();
        }
        return $senderEmail;
    }
}
