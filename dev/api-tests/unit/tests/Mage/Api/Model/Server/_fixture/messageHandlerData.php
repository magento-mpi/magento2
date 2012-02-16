<?php
/**
* Test model message handler options
*
* @category    Mage
* @package     Mage_Api
* @author      Magento Api Team <api-team@ebay.com>
*/
return array(
    'domains' => array(
        'success'       => array('http_code' => 200, 'type' => 'notification'),
        'processing'    => array('http_code' => 202, 'type' => 'notification'),
        'request_error' => array('http_code' => 400, 'type' => 'error'),
        'validation'    => array('http_code' => 400, 'type' => 'error'),
        'internal_error' => array('http_code' => 500, 'type' => 'error'),
    ),
    'messages' => array(
        'core' => array(
            'success' => array(
                'ok'         => 'Request is done.',
            ),
            'processing' => array(
                'processing' => 'Request in processing.'
            ),
            'validation' => array(
                'email_invalid'     => 'Email incorrect.',
                'password_confirm'  => 'Password incorrect.'
            ),
            'internal_error' => array(
                'unknown_error'     => 'Unknown error.'
            ),
        ),
        'webmany_payment' => array(
            'validation' => array(
                'email_invalid'     => 'WebMoney email is not valid.'
            ),
            'success' => array(
                'ok'     => 'WebMoney request is done.'
            ),
        ),
        'my_module' => array(
            'validation' => array(
                'email_invalid'     => 'My email is not valid.'
            ),
        ),
    ),
    'module' => 'my_module',
);
