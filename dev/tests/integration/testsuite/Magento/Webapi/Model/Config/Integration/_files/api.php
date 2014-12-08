<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return [
    'TestIntegration1' => [
        'resources' => [
            'Magento_Customer::manage',
            'Magento_Customer::online',
            'Magento_Sales::capture',
            'Magento_SalesRule::quote',
        ],
    ],
    'TestIntegration2' => [
        'resources' => ['Magento_Catalog::product_read', 'Magento_SalesRule::config_promo'],
    ],
    'TestIntegration3' => [
        'resources' => ['Magento_Catalog::product_read', 'Magento_Sales::create', 'Magento_SalesRule::quote'],
    ]
];
