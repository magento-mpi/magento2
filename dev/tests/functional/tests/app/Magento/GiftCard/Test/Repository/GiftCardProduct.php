<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class GiftCardProduct
 * Data for creation Gift Cart Product
 */
class GiftCardProduct extends AbstractRepository
{
    /**
     * Constructor
     *
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'name' => 'Test product giftcard %isolation%',
            'sku' => 'sku_test_product_giftcard_%isolation%',
            'giftcard_type' => 'Virtual',
            'giftcard_amounts' => [
                [
                    'website_id' => 'All Websites [USD]',
                    'price' => 120,
                ],
                [
                    'website_id' => 'All Websites [USD]',
                    'price' => 150,
                ]
            ],
            'quantity_and_stock_status' => [
                'qty' => 123.0000,
                'is_in_stock' => 'In Stock',
            ],
            'status' => 'Product online',
            'use_config_is_redeemable' => 'Yes',
            'use_config_lifetime' => 'Yes',
            'allow_message' => 'Yes',
            'use_config_allow_message' => 'Yes',
            'email_template' => 'Gift Card(s) Purchase (Default)',
            'use_config_email_template' => 'Yes',
            'visibility' => 'Catalog, Search',
            'url_key' => 'test-product-giftcard-%isolation%',
            'use_config_gift_message_available' => 'Yes',
            'use_config_gift_wrapping_available' => 'Yes',
            'gift_wrapping_price' => 100.00,
            'website_ids' => ['Main Website'],
            'attribute_set_id' => ['dataSet' => 'default']
        ];

        $this->_data['giftcard_product_with_price'] = [
            'name' => 'Test product giftcard %isolation%',
            'sku' => 'sku_test_product_giftcard_%isolation%',
            'giftcard_type' => 'Virtual',
            'price' => ['value' => '-', 'preset' => 'price_from-120'],
            'giftcard_amounts' => [
                [
                    'website_id' => 'All Websites [USD]',
                    'price' => 120,
                ],
                [
                    'website_id' => 'All Websites [USD]',
                    'price' => 150,
                ]
            ],
            'quantity_and_stock_status' => [
                'qty' => 123.0000,
                'is_in_stock' => 'In Stock',
            ],
            'status' => 'Product online',
            'use_config_is_redeemable' => 'Yes',
            'use_config_lifetime' => 'Yes',
            'allow_message' => 'Yes',
            'use_config_allow_message' => 'Yes',
            'email_template' => 'Gift Card(s) Purchase (Default)',
            'use_config_email_template' => 'Yes',
            'visibility' => 'Catalog, Search',
            'url_key' => 'test-product-giftcard-%isolation%',
            'use_config_gift_message_available' => 'Yes',
            'use_config_gift_wrapping_available' => 'Yes',
            'gift_wrapping_price' => 100.00,
            'website_ids' => ['Main Website'],
            'attribute_set_id' => ['dataSet' => 'default']
        ];
    }
}
