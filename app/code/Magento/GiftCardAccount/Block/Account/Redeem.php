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

class Redeem extends \Magento\Core\Block\Template
{
    /**
     * Customer balance data
     *
     * @var \Magento\CustomerBalance\Helper\Data
     */
    protected $_customerBalanceData = null;

    /**
     * @param \Magento\CustomerBalance\Helper\Data $customerBalanceData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CustomerBalance\Helper\Data $customerBalanceData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_customerBalanceData = $customerBalanceData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Stub for future ability to implement redeem limitations based on customer/settings
     *
     * @return boold
     */
    public function canRedeem()
    {
        return $this->_customerBalanceData->isEnabled();
    }

    /**
     * Retreive gift card code from url, empty if none
     *
     * @return string
     */
    public function getCurrentGiftcard()
    {
        $code = $this->getRequest()->getParam('giftcard', '');

        return $this->escapeHtml($code);
    }
}
