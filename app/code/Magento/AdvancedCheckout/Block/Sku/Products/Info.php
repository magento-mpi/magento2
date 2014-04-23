<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SKU failed information Block
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 *
 * @method \Magento\Sales\Model\Quote\Item getItem()
 */
namespace Magento\AdvancedCheckout\Block\Sku\Products;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * Checkout data
     *
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Product alert data
     *
     * @var \Magento\ProductAlert\Helper\Data
     */
    protected $_productAlertData = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\ProductAlert\Helper\Data $productAlertData
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\ProductAlert\Helper\Data $productAlertData,
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_productAlertData = $productAlertData;
        $this->_checkoutData = $checkoutData;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve item's message
     *
     * @return string
     */
    public function getMessage()
    {
        switch ($this->getItem()->getCode()) {
            case \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK:
                $message = '<span class="sku-out-of-stock" id="sku-stock-failed-' .
                    $this->getItem()->getId() .
                    '">' .
                    $this->_checkoutData->getMessage(
                        \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK
                    ) . '</span>';
                return $message;
            case \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED:
                $message = $this->_checkoutData->getMessage(
                    \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED
                );
                $message .= '<br/>' . __(
                    "Only %1%2%3 left in stock",
                    '<span class="sku-failed-qty" id="sku-stock-failed-' . $this->getItem()->getId() . '">',
                    $this->getItem()->getQtyMaxAllowed(),
                    '</span>'
                );
                return $message;
            case \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART:
                $item = $this->getItem();
                $message = $this->_checkoutData->getMessage(
                    \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART
                );
                $message .= '<br/>';
                if ($item->getQtyMaxAllowed()) {
                    $message .= __('The maximum quantity allowed for purchase is %1.', '<span class="sku-failed-qty" id="sku-stock-failed-' . $item->getId() . '">' . ($item->getQtyMaxAllowed()  * 1) . '</span>');
                } elseif ($item->getQtyMinAllowed()) {
                    $message .= __('The minimum quantity allowed for purchase is %1.', '<span class="sku-failed-qty" id="sku-stock-failed-' . $item->getId() . '">' . ($item->getQtyMinAllowed()  * 1) . '</span>');
                }
                return $message;
            default:
                $error = $this->_checkoutData->getMessage($this->getItem()->getCode());
                $error = $error ? $error : $this->escapeHtml($this->getItem()->getError());
                return $error ? $error : '';
        }
    }

    /**
     * Check whether item is 'SKU failed'
     *
     * @return bool
     */
    public function isItemSkuFailed()
    {
        return $this->getItem()->getCode() == \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU;
    }

    /**
     * Get not empty template only for failed items
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getItem()->getCode() ? parent::_toHtml() : '';
    }

    /**
     * Get configure/notification/other link
     *
     * @return string
     */
    public function getLink()
    {
        $item = $this->getItem();
        switch ($item->getCode()) {
            case \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_CONFIGURE:
                $link = $this->getUrl(
                    'checkout/cart/configureFailed',
                    array('id' => $item->getProductId(), 'qty' => $item->getQty(), 'sku' => $item->getSku())
                );
                return '<a href="' . $link . '" class="configure-popup">' . __(
                    "Specify the product's options"
                ) . '</a>';
            case \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK:
                /** @var $helper \Magento\ProductAlert\Helper\Data */
                $helper = $this->_productAlertData;

                if (!$helper->isStockAlertAllowed()) {
                    return '';
                }

                $helper->setProduct($this->getItem()->getProduct());
                $signUpLabel = $this->escapeHtml(__('Receive notice when item is restocked.'));
                return '<a href="' . $this->escapeHtml(
                    $helper->getSaveUrl('stock')
                ) . '" title="' . $signUpLabel . '">' . $signUpLabel . '</a>';
            default:
                return '';
        }
    }
    /**
     * Get tier price formatted with html
     *
     * @return string
     */
    public function getProductTierPriceHtml()
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\TierPrice::PRICE_CODE,
                $this->getItem()->getProduct(),
                [
                    'include_container' => true,
                    'zone' => \Magento\Pricing\Render::ZONE_ITEM_LIST
                ]
            );
        }

        return $price;
    }

    /**
     * @return \Magento\Pricing\Render
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default');
    }
}
