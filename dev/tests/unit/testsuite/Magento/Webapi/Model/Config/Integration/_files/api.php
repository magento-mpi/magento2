<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'TestIntegration1' => array(
        'resources' => array(
            'Magento_Customer::manage',
            'Magento_Customer::online',
            'Magento_Customer::order_statuses_read',
            'Magento_Sales::capture',
            'Magento_SalesHistory::history',
            'Magento_SalesArchive::add'
        )
    ),
    'TestIntegration2' => array(
        'resources' => array(
            'Magento_Catalog::product_read',
        )
    ),
);
