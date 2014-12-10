<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var \Magento\TestFramework\Application $this */

$cartPriceRulesCount = \Magento\TestFramework\Helper\Cli::getOption('cart_price_rules', 200);
$cartPriceRulesProductsFloor = \Magento\TestFramework\Helper\Cli::getOption('cart_price_rules_floor', 3);
$cartPriceRulesProductsFirstCategory = \Magento\TestFramework\Helper\Cli::getOption(
    'cart_price_rules_first_category',
    1
);

/** @var $model  \Magento\SalesRule\Model\Rule*/
$model = $this->getObjectManager()->get('Magento\SalesRule\Model\Rule');
$idField = $model->getIdFieldName();

for ($i = 0; $i < $cartPriceRulesCount; $i++) {
    $ruleName = sprintf('Shopping Cart Price Rule %1$d', $i);
    $data = [
        $idField => null,
        'product_ids' => '',
        'name' => $ruleName,
        'description' => '',
        'is_active' => '1',
        'website_ids' => [0 => '1'],
        'customer_group_ids' => [0 => '0', 1 => '1', 2 => '2', 3 => '3'],
        'coupon_type' => '1',
        'coupon_code' => '',
        'uses_per_customer' => '',
        'from_date' => '',
        'to_date' => '',
        'sort_order' => '',
        'is_rss' => '1',
        'rule' => [
            'conditions' => [
                1 => [
                    'type' => 'Magento\\SalesRule\\Model\\Rule\\Condition\\Combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ],
                '1--1' => [
                    'type' => 'Magento\\SalesRule\\Model\\Rule\\Condition\\Address',
                    'attribute' => 'total_qty',
                    'operator' => '>=',
                    'value' => $cartPriceRulesProductsFloor + $i,
                ],
                '1--2' => [
                    'type' => 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Found',
                    'value' => '1',
                    'aggregator' => 'all',
                    'new_child' => '',
                ],
                '1--2--1' => [
                    'type' => 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product',
                    'attribute' => 'category_ids',
                    'operator' => '==',
                    'value' => $cartPriceRulesProductsFirstCategory,
                ],
            ],
            'actions' => [
                1 => [
                    'type' => 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                ],
            ],
        ],
        'simple_action' => 'by_percent',
        'discount_amount' => '10',
        'discount_qty' => '0',
        'discount_step' => '',
        'apply_to_shipping' => '0',
        'simple_free_shipping' => '0',
        'stop_rules_processing' => '0',
        'reward_points_delta' => '',
        'store_labels' => [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
            7 => '',
            8 => '',
            9 => '',
            10 => '',
            11 => '',
        ],
        'page' => '1',
        'limit' => '20',
        'in_banners' => '',
        'banner_id' => ['from' => '', 'to' => ''],
        'banner_name' => '',
        'visible_in' => '',
        'banner_is_enabled' => '',
        'related_banners' => [],
    ];
    if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent' && isset($data['discount_amount'])) {
        $data['discount_amount'] = min(100, $data['discount_amount']);
    }
    if (isset($data['rule']['conditions'])) {
        $data['conditions'] = $data['rule']['conditions'];
    }
    if (isset($data['rule']['actions'])) {
        $data['actions'] = $data['rule']['actions'];
    }
    unset($data['rule']);

    $model->loadPost($data);
    $useAutoGeneration = (int)(!empty($data['use_auto_generation']));
    $model->setUseAutoGeneration($useAutoGeneration);
    $model->save();
}
