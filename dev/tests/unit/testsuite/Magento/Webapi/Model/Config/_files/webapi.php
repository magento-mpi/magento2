<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    '\Magento\TestModule1\Service\AllSoapAndRestV1Interface' => array(
        'class' => '\Magento\TestModule1\Service\AllSoapAndRestV1Interface',
        'baseUrl' => '/V1/testmodule1',
        'methods' => array(
            'item' => array(
                'httpMethod' => 'GET',
                'method' => 'item',
                'route' => '/:id',
                'isSecure' => false
            )
        )
    ),
    '\Magento\TestModule1\Service\AllSoapAndRestV2Interface' => array(
        'class' => '\Magento\TestModule1\Service\AllSoapAndRestV2Interface',
        'baseUrl' => '/V2/testmodule1',
        'methods' => array(
            'item' => array(
                'httpMethod' => 'GET',
                'method' => 'item',
                'route' => '/:id',
                'isSecure' => false
            ),
            'create' => array(
                'httpMethod' => 'POST',
                'method' => 'create',
                'route' => '',
                'isSecure' => false
            ),
            'delete' => array(
                'httpMethod' => 'DELETE',
                'method' => 'delete',
                'route' => '/:id',
                'isSecure' => true
            ),
        )
    ),
    '\Magento\TestModule1\Service\AllSoapAndRestV3Interface' => array(
        'class' => '\Magento\TestModule1\Service\AllSoapAndRestV3Interface',
        'baseUrl' => '/V3/testmodule1',
        'methods' => array()
    ),
);
