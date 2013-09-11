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

class Info extends \Magento\Core\Block\Template
{
    /**
     * Helper instance
     *
     * @var \Magento\AdvancedCheckout\Helper\Data|null
     */
    protected $_helper;

    /**
     * Retrieve helper instance
     *
     * @return \Magento\AdvancedCheckout\Helper\Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = \Mage::helper('Magento\AdvancedCheckout\Helper\Data');
        }
        return $this->_helper;
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
                $message = '<span class="sku-out-of-stock" id="sku-stock-failed-' . $this->getItem()->getId() . '">'
                    . $this->_getHelper()->getMessage(
                        \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK
                    ) . '</span>';
                return $message;
            case \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED:
                $message = $this->_getHelper()->getMessage(
                    \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED
                );
                $message .= '<br/>' . __("Only %1%2%3 left in stock", '<span class="sku-failed-qty" id="sku-stock-failed-' . $this->getItem()->getId() . '">', $this->getItem()->getQtyMaxAllowed(), '</span>');
                return $message;
            case \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART:
                $item = $this->getItem();
                $message = $this->_getHelper()->getMessage(
                    \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_QTY_ALLOWED_IN_CART
                );
                $message .= '<br/>';
                if ($item->getQtyMaxAllowed()) {
                    $message .= __('The maximum quantity allowed for purchase is %1.', '<span class="sku-failed-qty" id="sku-stock-failed-' . $item->getId() . '">' . ($item->getQtyMaxAllowed()  * 1) . '</span>');
                } else if ($item->getQtyMinAllowed()) {
                    $message .= __('The minimum quantity allowed for purchase is %1.', '<span class="sku-failed-qty" id="sku-stock-failed-' . $item->getId() . '">' . ($item->getQtyMinAllowed()  * 1) . '</span>');
                }
                return $message;
            default:
                $error = $this->_getHelper()->getMessage($this->getItem()->getCode());
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
        return $this->getItem()->getCode() ==  \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_SKU;
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
                $link = $this->getUrl('checkout/cart/configureFailed', array(
                    'id'  => $item->getProductId(),
                    'qty' => $item->getQty(),
                    'sku' => $item->getSku()
                ));
                return '<a href="' . $link . '" class="configure-popup">'
                        . __("Specify the product's options")
                        . '</a>';
            case \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_OUT_OF_STOCK:
                /** @var $helper \Magento\ProductAlert\Helper\Data */
                $helper = \Mage::helper('Magento\ProductAlert\Helper\Data');

                if (!$helper->isStockAlertAllowed()) {
                    return '';
                }

                $helper->setProduct($this->getItem()->getProduct());
                $signUpLabel = $this->escapeHtml(__('Receive notice when item is restocked.'));
                return '<a href="'
                    . $this->escapeHtml($helper->getSaveUrl('stock'))
                    . '" title="' . $signUpLabel . '">' . $signUpLabel . '</a>';
            default:
                return '';
        }
    }

    /**
     * Get html of tier price
     *
     * @return string
     */
    public function getTierPriceHtml()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->getItem()->getProduct();
        if (!$product || !$product->getId()) {
            return '';
        }

        $productTierPrices = $product->getData('tier_price');
        if (!is_array($productTierPrices)) {
            $productAttributes = $product->getAttributes();
            if (!isset($productAttributes['tier_price'])
                || !($productAttributes['tier_price'] instanceof \Magento\Catalog\Model\Resource\Eav\Attribute)
            ) {
                return '';
            }
            $productAttributes['tier_price']->getBackend()->afterLoad($product);
        }

        \Mage::unregister('product');
        \Mage::register('product', $product);
        if (!$this->hasProductViewBlock()) {
            $this->setProductViewBlock($this->getLayout()->createBlock('\Magento\Catalog\Block\Product\View'));
        }
        return $this->getProductViewBlock()->getTierPriceHtml();
    }
}
