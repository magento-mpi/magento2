<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Plugin;

class QuoteItem
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Append gift card additional data to order item options
     *
     * @param \Magento\Sales\Model\Convert\Quote $subject
     * @param callable $proceed
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     *
     * @return \Magento\Sales\Model\Order\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundItemToOrderItem(
        \Magento\Sales\Model\Convert\Quote $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Quote\Item\AbstractItem $item
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item);
        /** @var $quoteItem \Magento\Sales\Model\Quote\Item */
        $quoteItem = reset($arguments);

        $keys = array(
            'giftcard_sender_name',
            'giftcard_sender_email',
            'giftcard_recipient_name',
            'giftcard_recipient_email',
            'giftcard_message',
        );
        $productOptions = $orderItem->getProductOptions();
        foreach ($keys as $key) {
            $option = $quoteItem->getProduct()->getCustomOption($key);
            if ($option) {
                $productOptions[$key] = $option->getValue();
            }
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $quoteItem->getProduct();
        // set lifetime
        if ($product->getUseConfigLifetime()) {
            $lifetime = $this->_coreStoreConfig->getConfig(
                \Magento\GiftCard\Model\Giftcard::XML_PATH_LIFETIME,
                $orderItem->getStore()
            );
        } else {
            $lifetime = $product->getLifetime();
        }
        $productOptions['giftcard_lifetime'] = $lifetime;

        // set is_redeemable
        if ($product->getUseConfigIsRedeemable()) {
            $isRedeemable = $this->_coreStoreConfig->getConfigFlag(
                \Magento\GiftCard\Model\Giftcard::XML_PATH_IS_REDEEMABLE,
                $orderItem->getStore()
            );
        } else {
            $isRedeemable = (int)$product->getIsRedeemable();
        }
        $productOptions['giftcard_is_redeemable'] = $isRedeemable;

        // set email_template
        if ($product->getUseConfigEmailTemplate()) {
            $emailTemplate = $this->_coreStoreConfig->getConfig(
                \Magento\GiftCard\Model\Giftcard::XML_PATH_EMAIL_TEMPLATE,
                $orderItem->getStore()
            );
        } else {
            $emailTemplate = $product->getEmailTemplate();
        }
        $productOptions['giftcard_email_template'] = $emailTemplate;
        $productOptions['giftcard_type'] = $product->getGiftcardType();

        $orderItem->setProductOptions($productOptions);

        return $orderItem;
    }
}
