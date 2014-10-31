<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Block\Checkout\Cart\Item;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * Gift card catalog product configuration
     *
     * @var \Magento\GiftCard\Helper\Catalog\Product\Configuration
     */
    protected $_giftCardCtlgProdConfigur = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Core\Helper\Url $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\GiftCard\Helper\Catalog\Product\Configuration $giftCardCtlgProdConfigur
     * @param \Magento\Msrp\Helper\Data $msrpHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Core\Helper\Url $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\GiftCard\Helper\Catalog\Product\Configuration $giftCardCtlgProdConfigur,
        \Magento\Msrp\Helper\Data $msrpHelper,
        array $data = array()
    ) {
        $this->_giftCardCtlgProdConfigur = $giftCardCtlgProdConfigur;
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageHelper,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $msrpHelper,
            $data
        );
    }

    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        return $this->_giftCardCtlgProdConfigur->prepareCustomOption($this->getItem(), $code);
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        return $this->_giftCardCtlgProdConfigur->getGiftcardOptions($this->getItem());
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->_giftCardCtlgProdConfigur->getOptions($this->getItem());
    }
}
