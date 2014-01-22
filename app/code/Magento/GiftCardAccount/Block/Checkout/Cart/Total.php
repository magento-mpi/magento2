<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GiftCardAccount\Block\Checkout\Cart;

class Total extends \Magento\Checkout\Block\Total\DefaultTotal
{
    protected $_template = 'Magento_GiftCardAccount::cart/total.phtml';

    /**
     * @var \Magento\GiftCardAccount\Helper\Data|null
     */
    protected $_giftCardAccountData = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardAccountData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        array $data = array()
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct($context, $catalogData, $customerSession, $checkoutSession, $salesConfig, $data);
        $this->_isScopePrivate = true;
    }

    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return $this->_giftCardAccountData->getCards($this->getQuote());
    }
}
