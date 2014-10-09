<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\SalesRule\Model\Rule $salesRule */
$salesRule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\SalesRule\Model\Rule');
$salesRule->setData(
    array(
        'name' => '40% Off on Large Orders',
        'is_active' => 1,
        'customer_group_ids' => array(\Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID),
        'coupon_type' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON,
        'conditions' => array(
            array(
                'type' => 'Magento\SalesRule\Model\Rule\Condition\Address',
                'attribute' => 'base_subtotal',
                'operator' => '>',
                'value' => 800
            )
        ),
        'simple_action' => 'by_percent',
        'discount_amount' => 40,
        'stop_rules_processing' => 1,
        'website_ids' => array(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Framework\StoreManagerInterface'
            )->getWebsite()->getId()
        )
    )
);
$salesRule->save();
