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
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardAccountData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        $this->_giftCardAccountData = $giftCardAccountData;
        parent::__construct($catalogData, $coreData, $context, $coreConfig, $data);
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
