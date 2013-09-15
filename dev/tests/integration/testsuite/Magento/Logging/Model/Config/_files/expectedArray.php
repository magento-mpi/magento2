<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */


return array(
    'logging' => array(
        'actions'=>
        array(
            'apply_coupon'=>
            array(
                'label'=> 'Apply Coupon'
            ),
            'add_to_cart'=>
            array(
                'label'=> 'Add to Cart'
            )
        ),
        'enterprise_checkout'=>
        array(
            'label'=>
            'Shopping Cart Management',
            'expected_models'=>
            array(
                'Enterprise_GiftRegistry_Model_Entity'=>
                array(
                ),
                'Enterprise_GiftRegistry_Model_Item'=>
                array(
                )
            ),
            'actions'=>
            array(
                'adminhtml_checkout_index'=>
                array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'view',
                    'expected_models'=>
                    array(
                        'Magento_Sales_Model_Quote'=>
                        array(
                        )
                    )
                ),
                'adminhtml_checkout_applyCoupon'=>
                array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'apply_coupon',
                    'post_dispatch' => 'postDispatchAdminCheckoutApplyCoupon',
                    'expected_models'=>
                    array(
                        'Magento_Sales_Model_Quote'=>
                        array(
                        )
                    )
                ),
                'adminhtml_checkout_updateItems'=>
                array(
                    'group_name' => 'enterprise_checkout',
                    'action'=>
                    'save',
                    'skip_on_back'=>
                    array(
                        0=>
                        'adminhtml_cms_page_version_edit'
                    ),
                    'expected_models'=>
                    array(
                        'Magento_Sales_Model_Quote_Item'=>
                        array(
                        )
                    )
                ),
                'adminhtml_checkout_addToCart'=>
                array(
                    'group_name' => 'enterprise_checkout',
                    'action'=>
                    'add_to_cart',
                    'expected_models'=>
                    array(
                        'Magento_Sales_Model_Quote_Item'=>
                        array(
                            'additional_data'=>
                            array(

                                'item_id',

                                'quote_id'
                            )
                        )
                    )
                ),
                'adminhtml_customer_save'=>
                array(
                    'group_name' => 'enterprise_checkout',
                    'skip_on_back'=>
                    array(

                        'adminhtml_customerbalance_form',

                        'adminhtml_customer_edit'
                    ),
                    'expected_models'=>
                    array(
                        'Enterprise_CustomerBalance_Model_Balance'=>
                        array(
                        ),
                        '@'=> array(
                            'extends' => 'merge'
                        )
                    ),
                    'action' => 'save'
                ),
                'adminhtml_customersegment_match'=>
                array(
                    'group_name' => 'enterprise_checkout',
                    'action'=>
                    'refresh_data',
                    'post_dispatch'=>
                    'Enterprise_CustomerSegment_Model_Logging::postDispatchCustomerSegmentMatch'
                )
            )
        ),
        'customer'=>
        array(
            'label'=>
            'Customers',
            'expected_models'=>
            array(
                'Magento_Customer_Model_Customer'=>
                array(
                    'skip_data'=>
                    array(

                        'new_password',

                        'password',

                        'password_hash',
                    )
                )
            ),
            'actions'=>
            array(
                'adminhtml_customer_edit'=>
                array(
                    'group_name' => 'customer',
                    'action'=>
                    'view'
                ),
                'adminhtml_customer_save'=>
                array(
                    'group_name' => 'customer',
                    'action'=>
                    'save',
                    'skip_on_back'=>
                    array(

                        'adminhtml_customer_edit'
                    )
                )
            )
        )
    )
);
