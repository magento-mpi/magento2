<?php
/**
 * Model data
 *
 * @return array
 */
$keyCreate = substr(str_repeat(uniqid(), 6), 0, Mage_Oauth_Model_Consumer::KEY_LENGTH);
$secretCreate = substr(str_repeat(uniqid(), 6), 0, Mage_Oauth_Model_Consumer::SECRET_LENGTH);
$keyUpdate = substr(str_repeat(uniqid(), 6), 0, Mage_Oauth_Model_Consumer::KEY_LENGTH);
$secretUpdate = substr(str_repeat(uniqid(), 6), 0, Mage_Oauth_Model_Consumer::SECRET_LENGTH);
$date = date('Ymd-His');
return array(
    'create' => array(
        'name'          => $date . ' Consumer Name Create Test',
        'key'           => $keyCreate,
        'secret'        => $secretCreate,
    ),
    'update' => array(
        'name'          => $date . ' Consumer Name Update Test',
        'key'           => $keyUpdate,
        'secret'        => $secretUpdate,
        'callback_url'  => 'http://example.com/oauth_model/?oauthKey=key&oauthSecret=secret',
        'rejected_callback_url' => 'http://example.com/rejected_callback_url'
    ),
    'expected_create' => array(
        'entity_id'     => '',
        'name'          => $date . ' Consumer Name Create Test',
        'key'           => $keyCreate,
        'secret'        => $secretCreate,
        'callback_url'  => null,
        'rejected_callback_url' => ''
    ),
    'expected_update'  => array(
        'entity_id'     => '',
        'name'          => $date . ' Consumer Name Update Test',
        'key'           => $keyUpdate,
        'secret'        => $secretUpdate,
        'callback_url'  => 'http://example.com/oauth_model/?oauthKey=key&oauthSecret=secret',
        'rejected_callback_url' => 'http://example.com/rejected_callback_url'
    )
);
