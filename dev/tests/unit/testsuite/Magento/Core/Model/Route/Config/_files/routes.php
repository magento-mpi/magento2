<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'backend' => array(
        'id'        => 'backend',
        'routes'    => array(
            'adminhtml' => array(
                'id'        => 'adminhtml',
                'frontName' => 'admin',
                'modules'   => array(
                    'Magento_Sales',
                    'Magento_Catalog',
                    'Magento_Wishlist',
                    'Magento_Adminhtml',
                    'Magento_GiftCard',
                    'Magento_GiftCardAccount',
                ),
            ),
            'customer' => array(
                'id'        => 'customer',
                'frontName' => 'customer',
                'modules'   => array(
                    'Magento_Customer',
                ),
            ),
            'wishlist' => array(
                'id'        => 'wishlist',
                'frontName' => 'wishlist',
                'modules'   => array(
                    'Magento_Wishlist',
                ),
            ),
        ),
    ),
    'front' => array(
        'id'        => 'front',
    ),
);
