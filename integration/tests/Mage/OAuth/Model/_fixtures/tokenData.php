<?php
/**
 * Model data
 *
 * @return array
 */
$i = 10;
$keyData = substr(str_repeat(uniqid(), 6), 0, 62);
return array(
    'create' => array(
        'consumer_id'       => '',
        'admin_id'          => null,
        'tmp_token'         => $keyData . $i++,
        'tmp_token_secret'  => $keyData . $i++,
        'tmp_verifier'      => $keyData . $i++,
        'tmp_callback_url'  => 'http://example.com/oauth_model/?oauthKey=key&oauthSecret=secret',
        'token'             => $keyData . $i++,
        'token_secret'      => $keyData . $i++,
        'is_revoked'        => (string) rand(0, 1),
    ),
    'update' => array(
        'admin_id'          => null,
        'tmp_token'         => $keyData . $i++,
        'tmp_token_secret'  => $keyData . $i++,
        'tmp_verifier'      => $keyData . $i++,
        'tmp_callback_url'  => 'http://example.com/oauth_model/?oauthKey=key',
        'token'             => $keyData . $i++,
        'token_secret'      => $keyData . $i++,
        'is_revoked'        => (string) rand(0, 1),
    ),
);
