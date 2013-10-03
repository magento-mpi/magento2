<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Block\Checkout\Cart\Item;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * Gift card catalog product configuration
     *
     * @var \Magento\GiftCard\Helper\Catalog\Product\Configuration
     */
    protected $_giftCardCtlgProdConfigur = null;

    /**
     * @param \Magento\Catalog\Helper\Product\Configuration $ctlgProdConfigur
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\GiftCard\Helper\Catalog\Product\Configuration $giftCardCtlgProdConfigur
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Product\Configuration $ctlgProdConfigur,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\GiftCard\Helper\Catalog\Product\Configuration $giftCardCtlgProdConfigur,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_giftCardCtlgProdConfigur = $giftCardCtlgProdConfigur;
        parent::__construct($ctlgProdConfigur, $coreData, $context, $checkoutSession, $data);
    }

    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        return $this->_giftCardCtlgProdConfigur
            ->prepareCustomOption($this->getItem(), $code);
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        return $this->_giftCardCtlgProdConfigur
            ->getGiftcardOptions($this->getItem());
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->_giftCardCtlgProdConfigur
            ->getOptions($this->getItem());
    }
}
