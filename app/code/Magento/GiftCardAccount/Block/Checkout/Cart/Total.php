<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCardAccount\Block\Checkout\Cart;

class Total extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * @var string
     */
    protected $_template = 'Magento_GiftCardAccount::cart/total.phtml';

    /**
     * @var \Magento\GiftCardAccount\Helper\Data|null
     */
    protected $_giftCardAccountData = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardAccountData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        array $data = []
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct($context, $customerSession, $checkoutSession, $salesConfig, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Get sales quote
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * @return mixed
     */
    public function getQuoteGiftCards()
    {
        return $this->_giftCardAccountData->getCards($this->getQuote());
    }
}
