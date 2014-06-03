<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */


return array(
    'logging' => array(
        'actions' => array(
            'apply_coupon' => array('label' => 'Apply Coupon'),
            'add_to_cart' => array('label' => 'Add to Cart')
        ),
        'enterprise_checkout' => array(
            'label' => 'Shopping Cart Management',
            'expected_models' => array(
                'Enterprise_GiftRegistry_Model_Entity' => array(),
                'Enterprise_GiftRegistry_Model_Item' => array()
            ),
            'actions' => array(
                'adminhtml_checkout_index' => array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'view',
                    'controller_action' => 'adminhtml_checkout_index',
                    'expected_models' => array('Magento\Sales\Model\Quote' => array())
                ),
                'adminhtml_checkout_applyCoupon' => array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'apply_coupon',
                    'controller_action' => 'adminhtml_checkout_applyCoupon',
                    'post_dispatch' => 'postDispatchAdminCheckoutApplyCoupon',
                    'expected_models' => array('Magento\Sales\Model\Quote' => array())
                ),
                'adminhtml_checkout_updateItems' => array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'save',
                    'controller_action' => 'adminhtml_checkout_updateItems',
                    'skip_on_back' => array(0 => 'adminhtml_cms_page_version_edit'),
                    'expected_models' => array('Magento\Sales\Model\Quote\Item' => array())
                ),
                'adminhtml_checkout_addToCart' => array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'add_to_cart',
                    'controller_action' => 'adminhtml_checkout_addToCart',
                    'expected_models' => array(
                        'Magento\Sales\Model\Quote\Item' => array('additional_data' => array('item_id', 'quote_id'))
                    )
                ),
                'customer_index_save' => array(
                    'group_name' => 'enterprise_checkout',
                    'skip_on_back' => array('adminhtml_customerbalance_form', 'customer_index_edit'),
                    'expected_models' => array(
                        'Enterprise_CustomerBalance_Model_Balance' => array(),
                        '@' => array('extends' => 'merge')
                    ),
                    'action' => 'save',
                    'controller_action' => 'customer_index_save'
                ),
                'adminhtml_customersegment_match' => array(
                    'group_name' => 'enterprise_checkout',
                    'action' => 'refresh_data',
                    'post_dispatch' => 'Enterprise_CustomerSegment_Model_Logging::postDispatchCustomerSegmentMatch',
                    'controller_action' => 'adminhtml_customersegment_match'
                )
            )
        ),
        'customer' => array(
            'label' => 'Customers',
            'expected_models' => array(
                'Magento\Customer\Model\Customer' => array(
                    'skip_data' => array('new_password', 'password', 'password_hash')
                )
            ),
            'actions' => array(
                'customer_index_edit' => array(
                    'group_name' => 'customer',
                    'action' => 'view',
                    'controller_action' => 'customer_index_edit'
                ),
                'customer_index_save' => array(
                    'group_name' => 'customer',
                    'action' => 'save',
                    'controller_action' => 'customer_index_save',
                    'skip_on_back' => array('customer_index_edit')
                )
            )
        )
    )
);
