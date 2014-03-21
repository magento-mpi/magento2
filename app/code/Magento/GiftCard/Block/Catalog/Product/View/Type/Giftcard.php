<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Block\Catalog\Product\View\Type;

use Magento\Catalog\Model\Product;

class Giftcard extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Catalog\Helper\Product\Compare $compareProduct
     * @param \Magento\Theme\Helper\Layout $layoutHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     * @param array $priceBlockTypes
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Math\Random $mathRandom,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\Product\Compare $compareProduct,
        \Magento\Theme\Helper\Layout $layoutHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $catalogConfig,
            $registry,
            $taxData,
            $catalogData,
            $mathRandom,
            $cartHelper,
            $wishlistHelper,
            $compareProduct,
            $layoutHelper,
            $imageHelper,
            $arrayUtils,
            $data,
            $priceBlockTypes
        );
        $this->_isScopePrivate = true;
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getAmountSettingsJson($product)
    {
        $result = array('min'=>0, 'max'=>0);
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
     */
    public function getAmounts($product)
    {
        $result = array();
        foreach ($product->getGiftcardAmounts() as $amount) {
            $result[] = $this->_storeManager->getStore()->roundPrice($amount['website_value']);
        }
        sort($result);
        return $result;
    }

    /**
     * @return string
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
            return $this->_storeConfig->isSetFlag(\Magento\GiftCard\Model\Giftcard::XML_PATH_ALLOW_MESSAGE, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
        } else {
            return (int) $product->getAllowMessage();
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
        $firstName = (string)$this->_customerSession->getCustomer()->getFirstname();
        $lastName  = (string)$this->_customerSession->getCustomer()->getLastname();

        if ($firstName && $lastName) {
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
        return (string)$this->_customerSession->getCustomer()->getEmail();
    }

    /**
     * @return int
     */
    public function getMessageMaxLength()
    {
        return (int) $this->_storeConfig->getValue(\Magento\GiftCard\Model\Giftcard::XML_PATH_MESSAGE_MAX_LENGTH, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }

    /**
     * Returns default value to show in input
     *
     * @param string $key
     * @return string
     */
    public function getDefaultValue($key)
    {
        return (string) $this->getProduct()->getPreconfiguredValues()->getData($key);
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
