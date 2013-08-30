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
                    'Mage_Sales',
                    'Mage_Catalog',
                    'Mage_Wishlist',
                    'Mage_Adminhtml',
                    'Mage_GiftCard',
                    'Mage_GiftCardAccount',
                ),
            ),
            'customer' => array(
                'id'        => 'customer',
                'frontName' => 'customer',
                'modules'   => array(
                    'Mage_Customer',
                ),
            ),
            'wishlist' => array(
                'id'        => 'wishlist',
                'frontName' => 'wishlist',
                'modules'   => array(
                    'Mage_Wishlist',
                ),
            ),
        ),
    ),
    'front' => array(
        'id'        => 'front',
    ),
);
