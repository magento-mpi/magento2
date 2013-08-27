<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
return array(
    array(
        'id' => 'Magento_Webapi',
        'children' => array(
            array(
                'id' => 'customer',
                'title' => 'Manage Customers',
                'sortOrder' => 20,
                'children' => array(
                    array(
                        'id' => 'customer/create',
                        'title' => 'Create Customer',
                        'sortOrder' => '30',
                        'children' => array(),
                    ),
                    array(
                        'id' => 'customer/update',
                        'title' => 'Edit Customer',
                        'sortOrder' => '10',
                        'children' => array(),
                    ),
                    array(
                        'id' => 'customer/get',
                        'title' => 'Get Customer',
                        'sortOrder' => '20',
                        'children' => array(),
                    ),
                    array(
                        'id' => 'customer/delete',
                        'title' => 'Delete Customer',
                        'children' => array(),
                    ),
                )
            )
        )
    )
);
