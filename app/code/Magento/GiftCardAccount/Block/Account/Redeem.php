<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Account;

class Redeem extends \Magento\View\Element\Template
{
    /**
     * Customer balance data
     *
     * @var \Magento\CustomerBalance\Helper\Data
     */
    protected $_customerBalanceData = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\CustomerBalance\Helper\Data $customerBalanceData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\CustomerBalance\Helper\Data $customerBalanceData,
        array $data = array()
    ) {
        $this->_customerBalanceData = $customerBalanceData;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Stub for future ability to implement redeem limitations based on customer/settings
     *
     * @return bool
     */
    public function canRedeem()
    {
        return $this->_customerBalanceData->isEnabled();
    }

    /**
     * Retrieve gift card code from url, empty if none
     *
     * @return string
     */
    public function getCurrentGiftcard()
    {
        $code = $this->getRequest()->getParam('giftcard', '');

        return $this->escapeHtml($code);
    }
}
