<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Fixture\GiftCardProduct;

/**
 * Class CheckoutData
 * Data for fill product form on frontend
 *
 * Data keys:
 *  - preset (Checkout data verification preset name)
 */
class CheckoutData extends \Magento\Catalog\Test\Fixture\CatalogProductSimple\CheckoutData
{
    /**
     * Get preset array
     *
     * @param $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'options' => [
                    'giftcard_options' => [
                        'giftcard_amount' => 'option_key_1',
                        'giftcard_sender_name' => 'Sender_name_%isolation%',
                        'giftcard_sender_email' => 'Sender_name_%isolation%@example.com',
                        'giftcard_recipient_name' => 'Recipient_name_%isolation%',
                        'giftcard_recipient_email' => 'Recipient_name_%isolation%@example.com',
                        'giftcard_message' => 'Message text %isolation%',
                    ],
                    'qty' => '1',
                ],
                'cartItem' => [
                    'price' => 150,
                    'qty' => 1,
                    'subtotal' => 150
                 ]
            ],
        ];
        return isset($presets[$name]) ? $presets[$name] : null;
    }
}
