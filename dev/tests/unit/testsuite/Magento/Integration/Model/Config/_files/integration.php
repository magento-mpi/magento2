<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'TestIntegration1' => array(
        'name' => 'Test Integration 1',
        'email' => 'test-integration1@magento.com',
        'authentication' => array(
            'type' => 'oauth',
            'endpoint_url' => 'http://endpoint.com'
        )
    ),
    'TestIntegration2' => array(
        'name' => 'Test Integration 2',
        'email' => 'test-integration2@magento.com',
        'authentication' => array(
            'type' => 'manual'
        )
    ),
);
