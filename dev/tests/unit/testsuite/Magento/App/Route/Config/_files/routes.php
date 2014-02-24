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
                    'Magento_ModuleA',
                    'Magento_ModuleB',
                    'Magento_ModuleC',
                    'Magento_ModuleD',
                    'Magento_ModuleE',
                    'Magento_ModuleF',
                ),
            ),
            'customer' => array(
                'id'        => 'customer',
                'frontName' => 'customer',
                'modules'   => array(
                    'Magento_ModuleE',
                ),
            ),
            'wishlist' => array(
                'id'        => 'wishlist',
                'frontName' => 'wishlist',
                'modules'   => array(
                    'Magento_ModuleC',
                ),
            ),
        ),
    ),
    'front' => array(
        'id'        => 'front',
    ),
);
