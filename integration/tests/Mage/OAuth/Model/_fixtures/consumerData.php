<?php
/**
 * Model data
 *
 * @return array
 */
$keyCreate = substr(str_repeat(uniqid(), 6), 0, Mage_OAuth_Model_Consumer::KEY_LENGTH);
$secretCreate = substr(str_repeat(uniqid(), 6), 0, Mage_OAuth_Model_Consumer::SECRET_LENGTH);
$keyUpdate = substr(str_repeat(uniqid(), 6), 0, Mage_OAuth_Model_Consumer::KEY_LENGTH);
$secretUpdate = substr(str_repeat(uniqid(), 6), 0, Mage_OAuth_Model_Consumer::SECRET_LENGTH);
return array(
    'create' => array(
        'name'          => 'Consumer Name Create Test',
        'key'           => $keyCreate,
        'secret'        => $secretCreate,
    ),
    'update' => array(
        'name'          => 'Consumer Name Update Test',
        'key'           => $keyUpdate,
        'secret'        => $secretUpdate,
        'call_back_url' => 'http://example.com/oauth_model/?oauthKey=key&oauthSecret=secret',
    ),
    'expected_create' => array(
        'entity_id'     => '',
        'name'          => 'Consumer Name Create Test',
        'key'           => $keyCreate,
        'secret'        => $secretCreate,
        'call_back_url' => null,
    ),
    'expected_update' => array(
        'entity_id'     => '',
        'name'          => 'Consumer Name Update Test',
        'key'           => $keyUpdate,
        'secret'        => $secretUpdate,
        'call_back_url' => 'http://example.com/oauth_model/?oauthKey=key&oauthSecret=secret',
    )
);
