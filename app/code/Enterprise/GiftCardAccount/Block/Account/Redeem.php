<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Account_Redeem extends Magento_Core_Block_Template
{
    /**
     * Stub for future ability to implement redeem limitations based on customer/settings
     *
     * @return boold
     */
    /**
     * Customer balance data
     *
     * @var Enterprise_CustomerBalance_Helper_Data
     */
    protected $_customerBalanceData = null;

    /**
     * @param Enterprise_CustomerBalance_Helper_Data $customerBalanceData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_CustomerBalance_Helper_Data $customerBalanceData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerBalanceData = $customerBalanceData;
        parent::__construct($coreData, $context, $data);
    }

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
