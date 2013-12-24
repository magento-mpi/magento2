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
        'methods' => array(
            'item' => array(
                'httpMethod' => 'GET',
                'method' => 'item',
                'route' => '/:id',
                'isSecure' => false,
                'resources' => array('Magento_TestModule1::resource1'),
            ),
            'create' => array(
                'httpMethod' => 'POST',
                'method' => 'create',
                'route' => '',
                'isSecure' => false,
                'resources' => array('Magento_TestModule1::resource2'),
            ),
        ),
        'baseUrl' => '/V1/testmodule1',
    ),
    '\Magento\TestModule1\Service\AllSoapAndRestV2Interface' => array(
        'class' => '\Magento\TestModule1\Service\AllSoapAndRestV2Interface',
        'methods' => array(
            'item' => array(
                'httpMethod' => 'GET',
                'method' => 'item',
                'route' => '/:id',
                'isSecure' => false,
                'resources' => array('Magento_TestModule1::resource1'),
            ),
            'create' => array(
                'httpMethod' => 'POST',
                'method' => 'create',
                'route' => '',
                'isSecure' => false,
                'resources' => array('Magento_TestModule1::resource1', 'Magento_TestModule1::resource2'),
            ),
            'delete' => array(
                'httpMethod' => 'DELETE',
                'method' => 'delete',
                'route' => '/:id',
                'isSecure' => true,
                'resources' => array('Magento_TestModule1::resource2'),
            ),
        ),
        'baseUrl' => '/V2/testmodule1',
    ),
    '\Magento\TestModule2\Service\AllSoapAndRestV2Interface' => array(
        'class' => '\Magento\TestModule2\Service\AllSoapAndRestV2Interface',
        'methods' => array(
            'update' => array(
                'httpMethod' => 'PUT',
                'method' => 'update',
                'route' => '',
                'isSecure' => false,
                'resources' => array('Magento_TestModule1::resource1'),
            ),
            'delete' => array(
                'httpMethod' => 'DELETE',
                'method' => 'delete',
                'route' => '/:id',
                'isSecure' => true,
                'resources' => array('Magento_TestModule1::resource2'),
            ),
        ),
        'baseUrl' => '/V2/testmodule2',
    ),
);
