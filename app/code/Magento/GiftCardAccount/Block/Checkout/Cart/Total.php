<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Checkout\Cart;

use Magento\Customer\Service\V1\CustomerServiceInterface as CustomerService;

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
     * @param CustomerService $customerService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        CustomerService $customerService,
        array $data = array()
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct(
            $context,
            $catalogData,
            $customerSession,
            $checkoutSession,
            $salesConfig,
            $customerService,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Get sales quoute
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return $this->_giftCardAccountData->getCards($this->getQuote());
    }
}
