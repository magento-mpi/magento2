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
     * @var Magento_GiftCardAccount_Helper_Data|null
     */
    protected $_giftCardAccountData = null;

    /**
     * @param Magento_GiftCardAccount_Helper_Data $giftCardAccountData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GiftCardAccount_Helper_Data $giftCardAccountData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct($catalogData, $coreData, $context, $data);
    }

    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
    }

    public function getQuoteGiftCards()
    {
        return $this->_giftCardAccountData->getCards($this->getQuote());
    }
}
