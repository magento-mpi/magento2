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
     * @var Magento_CustomerBalance_Helper_Data
     */
    protected $_customerBalanceData = null;

    /**
     * @param Magento_CustomerBalance_Helper_Data $customerBalanceData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerBalance_Helper_Data $customerBalanceData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
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
