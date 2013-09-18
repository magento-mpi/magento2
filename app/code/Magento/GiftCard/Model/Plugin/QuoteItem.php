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
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Append gift card additional data to order item options
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundItemToOrderItem(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $invocationChain->proceed($arguments);
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

        $product = $quoteItem->getProduct();
        // set lifetime
        if ($product->getUseConfigLifetime()) {
            $lifetime = $this->_coreStoreConfig->getConfig(
                Magento_GiftCard_Model_Giftcard::XML_PATH_LIFETIME,
                $orderItem->getStore()
            );
        } else {
            $lifetime = $product->getLifetime();
        }
        $productOptions['giftcard_lifetime'] = $lifetime;

        // set is_redeemable
        if ($product->getUseConfigIsRedeemable()) {
            $isRedeemable = $this->_coreStoreConfig->getConfigFlag(
                Magento_GiftCard_Model_Giftcard::XML_PATH_IS_REDEEMABLE,
                $orderItem->getStore()
            );
        } else {
            $isRedeemable = (int)$product->getIsRedeemable();
        }
        $productOptions['giftcard_is_redeemable'] = $isRedeemable;

        // set email_template
        if ($product->getUseConfigEmailTemplate()) {
            $emailTemplate = $this->_coreStoreConfig->getConfig(
                Magento_GiftCard_Model_Giftcard::XML_PATH_EMAIL_TEMPLATE,
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
