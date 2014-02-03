<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    '\Magento\TestModule1\Service\V1\AllSoapAndRestInterface' => array(
        'class' => '\Magento\TestModule1\Service\V1\AllSoapAndRestInterface',
        'baseUrl' => '/V1/testmodule1',
        'methods' => array(
            'item' => array(
                'httpMethod' => 'GET',
                'method' => 'item',
                'route' => '/:id',
                'isSecure' => false,
                'resources' => array('Magento_Test1::resource1')
            )
        )
    ),
    '\Magento\TestModule1\Service\V2\AllSoapAndRestInterface' => array(
        'class' => '\Magento\TestModule1\Service\V2\AllSoapAndRestInterface',
        'baseUrl' => '/V2/testmodule1',
        'methods' => array(
            'item' => array(
                'httpMethod' => 'GET',
                'method' => 'item',
                'route' => '/:id',
                'isSecure' => false,
                'resources' => array('Magento_Test1::resource1', 'Magento_Test1::resource2')
            ),
            'create' => array(
                'httpMethod' => 'POST',
                'method' => 'create',
                'route' => '',
                'isSecure' => false,
                'resources' => array('Magento_Test1::resource1', 'Magento_Test1::resource2')
            ),
            'delete' => array(
                'httpMethod' => 'DELETE',
                'method' => 'delete',
                'route' => '/:id',
                'isSecure' => true,
                'resources' => array('Magento_Test1::resource2')
            ),
        )
    ),
    '\Magento\TestModule1\Service\V3\AllSoapAndRestInterface' => array(
        'class' => '\Magento\TestModule1\Service\V3\AllSoapAndRestInterface',
        'methods' => array()
    ),
);
